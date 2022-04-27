<?php

namespace App\Console\Commands;

use App\Discord\SessionFactory;
use App\TowerDefense\GameManagerSession;
use Discord\Discord;
use Illuminate\Console\Command;

class TowerDefenseProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tower-defense:process {channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to the tower defense game.';

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
     * @param SessionFactory $sessionFactory
     * @return mixed
     */
    public function handle(SessionFactory $sessionFactory)
    {
        $discord = new Discord([
            'token' => config('discord.token'),
        ]);

        $session = $sessionFactory->create(GameManagerSession::class, $discord, [
            'channelId' => $this->argument('channel'),
        ]);

        $session->open();
    }
}
