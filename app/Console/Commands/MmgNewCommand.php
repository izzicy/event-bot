<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\Draw\StandardGameDrawer;
use App\Mmg\GameRepository;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Intervention\Image\ImageManagerStatic;

class MmgNewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mmg:new {width} {height} {minecount} {--channel=}';

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

        /** @var GameRepository */
        $gameRepository = app(GameRepository::class);

        /** @var FactoryInterface */
        $factory = app(FactoryInterface::class);

        $game = $gameRepository->create(
            $this->argument('width'),
            $this->argument('height'),
            $this->argument('minecount')
        );

        $drawer = new StandardGameDrawer($factory);

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
