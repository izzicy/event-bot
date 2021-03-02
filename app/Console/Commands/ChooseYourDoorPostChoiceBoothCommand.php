<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Services\ChooseYourDoorGame\DiscordConnection;
use App\Services\ChooseYourDoorGame\DoorImageCreator;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;

class ChooseYourDoorPostChoiceBoothCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'choose-your-door:post-choice-booth {--channel-id=} {--door-count=4}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post the choose-your-door choice booth.';

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

        $pipelines = [
            new OnDiscordReadyMiddleware($discord),

            function($passable, $next) use ($discord) {
                $channelId = $this->option('channel-id') ?? config('choose-your-door-game.default-channel');
                $doorCount = $this->option('door-count');
                $discordConnection = new DiscordConnection();

                $path = app(DoorImageCreator::class)->create($doorCount);

                $discordConnection->postChoiceBooth($discord, $channelId, $path, $doorCount)->then(function() use ($next) {
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
