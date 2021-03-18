<?php

namespace App\Console\Commands;

use Discord\Discord;
use Illuminate\Console\Command;

class MessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message {message} {--channel=} {--image=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message.';

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

        $discord->on('ready', function(Discord $discord) {
            $channelId = $this->option('channel') ?? config('choose-your-door-game.default-channel');
            $channel = $discord->getChannel($channelId);

            $image = $this->option('image');
            $message = str_replace('\\n', "\n", $this->argument('message'));

            if ($image) {
                $channel->sendFile($image, null, $message)->then(function() use ($discord) {
                    $discord->close();
                });
            } else {
                $channel->sendMessage($message)->then(function() use ($discord) {
                    $discord->close();
                });

            }
        });

        $discord->run();
    }
}
