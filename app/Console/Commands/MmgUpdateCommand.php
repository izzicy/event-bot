<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Mmg\Commands\FlagCommand;
use App\Mmg\Commands\PickTileCommand;
use App\Mmg\Commands\UnflagCommand;
use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\Draw\StandardGameDrawer;
use App\Mmg\Draw\UiDrawer;
use App\Mmg\GameRepository;
use App\Services\Users\Retrieval\Collector;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Intervention\Image\ImageManagerStatic;

class MmgUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mmg:update {game} {message} {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the game';

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

        $channelId = $this->option('channel') ?? config('mmg.default-channel');
        $messageId = $this->argument('message');

        /** @var GameRepository */
        $gameRepository = app(GameRepository::class);

        /** @var FactoryInterface */
        $factory = app(FactoryInterface::class);

        /** @var Collector */
        $collector = app(Collector::class);

        $game = $gameRepository->find($this->argument('game'));

        $command = $factory->createAggregateCommand([
            new PickTileCommand($factory),
            new FlagCommand(),
            new UnflagCommand(),
        ]);

        $pipelines = [
            new OnDiscordReadyMiddleware($discord),

            // Load all users.
            function($passable, Closure $next) use ($discord, $collector) {
                $collector->getUsers()->loadUsersIfMissing($discord)->done(function() use ($next) {
                    $next(null);
                });
                $collector->unsubscribe();
            },

            // Get the user choices
            function($passable, Closure $next) use ($discord, $messageId, $channelId) {
                $channel = $discord->getChannel($channelId);

                $channel->getMessageHistory([
                    'after' => $messageId,
                ])->done(function($messages) use ($next) {
                    $next($messages);
                });
            },

            // Get the user choices
            function($discordMessages, Closure $next) use ($command, $game, $factory, $gameRepository) {
                $messages = $factory->createMessagesFromDiscord($discordMessages);

                foreach ($messages as $message) {
                    $command->handleMessage($message);
                }

                $command->operateGame($game);

                $gameRepository->persist($game);

                $next(null);
            },

            // Get the user choices
            function($passable, Closure $next) use ($discord, $game, $factory, $channelId) {
                $drawer = new UiDrawer($factory);

                $channel = $discord->getChannel($channelId);
                $image = ImageManagerStatic::make($drawer->draw($game));
                $path = tempnam(sys_get_temp_dir(), '') . '.png';
                $image->save($path);

                $channel->sendFile($path)->done(function() use ($next) {
                    $next(null);
                });
            },

            // Get the user choices
            function($passable, Closure $next) use ($discord, $game, $factory, $channelId) {
                $drawer = new StandardGameDrawer($factory);

                $channel = $discord->getChannel($channelId);
                $image = ImageManagerStatic::make($drawer->draw($game));
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
