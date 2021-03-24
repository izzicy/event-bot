<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;

class AmogusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amogus {channel} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add amogus.';

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
                $channelId = $this->argument('channel');
                $messageId = $this->argument('message');

                $channel = $discord->getChannel($channelId);
                $channel->getMessage($messageId)->done(function($message) use ($next) {
                    $next($message);
                });
            },

            function(Message $message, $next) {
                $amogus = [
                    '🇦',
                    '🇲',
                    '🇴',
                    '🇬',
                    '🇺',
                    '🇸',
                ];

                $reactions = [];

                foreach ($amogus as $amogi) {
                    $reactions[] = function($passable, $next) use ($message, $amogi) {
                        $message->react($amogi)->then(function() use ($next) {
                            $next(null);
                        });
                    };
                }

                (new Pipeline(app()))
                    ->through($reactions)
                    ->then(function() use ($next) {
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
