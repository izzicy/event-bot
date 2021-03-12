<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Models\MultiplayerMinesweeper\MinesweeperGame;
use App\Services\MMGame\Factory;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;

class MmgNewGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mmg:new-game {width} {height} {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new mmg game.';

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

        $game = MinesweeperGame::createNewGame(
            $this->argument('width'),
            $this->argument('height')
        );

        /** @var Factory */
        $factory = app(Factory::class);
        $drawer = $factory->createGameDrawer();

        $pipelines = [
            new OnDiscordReadyMiddleware($discord),

            // Get the user choices
            function($passable, Closure $next) use ($discord, $drawer, $channelId, $game) {
                $channel = $discord->getChannel($channelId);

                $channel->sendFile($drawer->draw($game))->done(function() use ($next) {
                    $next(null);
                });;
            },

            new DiscordCloseMiddleware($discord),
        ];

        (new Pipeline(app()))
            ->through($pipelines)
            ->thenReturn();

        $discord->run();
    }
}
