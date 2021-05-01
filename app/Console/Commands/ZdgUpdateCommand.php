<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Services\Messages\Factory;
use App\Zdg\Draw\DrawGame;
use App\Zdg\Models\Game;
use App\Zdg\PixelColourerCommand;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Intervention\Image\ImageManagerStatic;

class ZdgUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zdg:update {game} {--message=} {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the zero dollar game.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $discord = new Discord([
            'token' => config('discord.token'),
        ]);

        $channelId = $this->option('channel') ?? config('zdg.default-channel');
        $messageId = $this->option('message');

        $game = Game::find($this->argument('game'));

        $pipelines = [
            new OnDiscordReadyMiddleware($discord),

            // Get the user choices
            function($passable, Closure $next) use ($discord, $messageId, $channelId) {
                $channel = $discord->getChannel($channelId);

                $options = [];

                if ($messageId) {
                    $options['after'] = $messageId;
                }

                $channel->getMessageHistory($options)->done(function($messages) use ($next) {
                    $next($messages);
                });
            },

            // Interpret the user choices.
            function($discordMessages, Closure $next) use ($game, $discord) {
                try {
                    $messages = app(Factory::class)->createFromDirectResponses($discordMessages, $discord->id);

                    $command = new PixelColourerCommand();

                    foreach ($messages as $message) {
                        $command->handleMessage($message);
                    }

                    $command->operateGame($game);

                    $next(null);
                }
                catch (\Throwable $e) {
                    dd($e);
                }
            },

            // post the update
            function($passable, Closure $next) use ($discord, $game, $channelId) {
                $drawer = new DrawGame();

                $game->refresh();

                $channel = $discord->getChannel($channelId);
                $image = ImageManagerStatic::make($drawer->draw($game));
                $path = tempnam(sys_get_temp_dir(), '') . '.png';
                $image->save($path);

                $channel->sendFile($path)->done(function() use ($next) {
                    $next(null);
                });
            },

            // post the update with grid
            function($passable, Closure $next) use ($discord, $game, $channelId) {
                $drawer = new DrawGame();

                $game->refresh();

                $channel = $discord->getChannel($channelId);
                $image = ImageManagerStatic::make($drawer->draw($game, true));
                $path = tempnam(sys_get_temp_dir(), '') . '.png';
                $image->save($path);

                $channel->sendFile($path)->done(function() use ($next) {
                    $next(null);
                });
            },

            new DiscordCloseMiddleware($discord),
        ];

        (new Pipeline(app()))
            ->through($pipelines)
            ->thenReturn();

        $discord->run();
    }
}
