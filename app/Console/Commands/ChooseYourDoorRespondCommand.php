<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Services\Choices\ChoicesResultsInterface;
use App\Services\ChooseYourDoorGame\DiscordConnection;
use App\Services\ChooseYourDoorGame\PhraseCreator;
use App\Services\ChooseYourDoorGame\ResultsImageCreator;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

class ChooseYourDoorRespondCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'choose-your-door:respond {--channel=} {--message=} {--door-count=4} {--correct-doors=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respond to the choices made in the choice booth.';

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

        $channelId = $this->option('channel') ?? config('choose-your-door-game.default-channel');
        $messageId = $this->option('message');

        $discordConnection = new DiscordConnection();

        $pipelines = [
            new OnDiscordReadyMiddleware($discord),

            // Get the user choices
            function($passable, Closure $next) use ($discord, $discordConnection, $channelId, $messageId) {
                $discordConnection->getUserChoices($discord, $channelId, $messageId)->then(function($choices) use ($next) {
                    $next($choices);
                });
            },

            // Send the losers + winners message
            function($choices, Closure $next) use ($discord, $discordConnection, $channelId) {
                try {
                    $users = collect($choices->getUsers());

                    // $channelId= '811689892038836247';

                    $doorCount = $this->option('door-count');

                    if (empty($users)) {
                        $discord->close();
                    }

                    $commandChoices = collect(range(1, $doorCount))->map(function($count) {
                        return 'door-' . $count;
                    });

                    $correctChoices = $commandChoices->random($this->option('correct-doors'));

                    $this->info('Chosen doors were: ' . $correctChoices->join(', '));

                    $message = app(PhraseCreator::class)->create($users, $choices, $correctChoices);
                    $imagePath = app(ResultsImageCreator::class)->create($users, $choices, $correctChoices, $doorCount);

                    $discordConnection->postLosers($discord, $channelId, $message, $imagePath)->then(function() use ($next) {
                        $next(null);
                    });
                }
                catch(\Throwable $e) {
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
}
