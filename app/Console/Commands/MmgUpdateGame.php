<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Models\MultiplayerMinesweeper\MinesweeperGame;
use App\Services\MMGame\Contracts\UserAssocTilesCollectionInterface;
use App\Services\MMGame\Factory;
use App\Services\Users\DiscordUserCollection;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;

class MmgUpdateGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mmg:update-game {game} {message} {--channel=}';

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
        $game = MinesweeperGame::find($this->argument('game'));

        /** @var Factory */
        $factory = app(Factory::class);
        $drawer = $factory->createGameDrawer();

        $pipelines = [
            new OnDiscordReadyMiddleware($discord),

            // Get the user choices
            function($passable, Closure $next) use ($discord, $messageId, $channelId, $game) {
                $channel = $discord->getChannel($channelId);

                $channel->getMessageHistory([
                    'after' => $messageId,
                ])->done(function($messages) use ($next) {
                    $next($messages);
                });
            },

            // Get the user choices
            function($messages, Closure $next) use ($discord, $drawer, $channelId, $game, $factory) {
                try {
                    $parser = $factory->createUserCommandsParserFromDiscordMessages($messages, $game);
                    $picks = $parser->createUserTilePicks();

                    $this->initializeIfNotInitialized($game, $factory, $picks);

                    $conqueredPicks = $factory->createConquerer($game, $game->grid, $picks)->getConqueredPicks();

                    $game->createConqueredTilesFrom($conqueredPicks);

                    $users = new DiscordUserCollection();

                    $game->refresh();

                    foreach ($game->conquered as $conquered) {
                        $users->push($conquered->user);
                    }

                    $users->loadUsersIfMissing($discord)->done(function() use ($next) {
                        $next(null);
                    });
                }
                catch (\Throwable $e) {
                    dd($e);
                }
            },

            function($passable, $next) use ($discord, $drawer, $channelId, $game, $factory)  {
                try {
                    $channel = $discord->getChannel($channelId);

                    $channel->sendFile($drawer->draw($game))->done(function() use ($next) {
                        $next(null);
                    });
                }
                catch (\Throwable $e) {
                    dd($e);
                }
            },

            new DiscordCloseMiddleware($discord),
        ];

        (new Pipeline(app()))
            ->through($pipelines)
            ->thenReturn();

        $discord->run();
    }

    /**
     * Initalize the game if it hadn't initilized yet.
     *
     * @param MinesweeperGame $game
     * @param Factory $factory
     * @param UserAssocTilesCollectionInterface $picks
     * @return void
     */
    protected function initializeIfNotInitialized(MinesweeperGame $game, Factory $factory, $picks)
    {
        if ($game->initialized === false) {
            $game->initialized = true;
            $game->save();

            $distributer = $factory->createMineDistributer();

            $distributer->distribute($game->grid, $picks, 40);

            $game->grid->save();
        }
    }
}
