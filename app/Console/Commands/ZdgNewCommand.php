<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Zdg\Draw\DrawGame;
use App\Zdg\Models\Game;
use App\Zdg\Models\Pixel;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Intervention\Image\ImageManagerStatic;

class ZdgNewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zdg:new {width} {height} {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new zero dollar game.';

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

        $game = Game::create([
            'width' => $this->argument('width'),
            'height' => $this->argument('height')
        ]);

        $drawer = new DrawGame();

        $pipelines = [
            new OnDiscordReadyMiddleware($discord),

            // Get the user choices
            function($passable, Closure $next) use ($discord, $drawer, $channelId, $game) {
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
            ->then(function() use ($game) {
                $this->info('Game id: ' . $game->getKey());
            });

        $discord->run();
    }
}
