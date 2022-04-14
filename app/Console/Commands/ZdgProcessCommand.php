<?php

namespace App\Console\Commands;

use App\Discord\DiscordCloseMiddleware;
use App\Discord\OnDiscordReadyMiddleware;
use App\Services\Messages\Factory;
use App\Zdg\ChoicesAggregate;
use App\Zdg\Draw\DrawGame;
use App\Zdg\FromImageColourerCommand;
use App\Zdg\Models\Game;
use App\Zdg\PixelColourerCommand;
use App\Zdg\ZdgSession;
use Closure;
use Discord\Discord;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Intervention\Image\ImageManagerStatic;

class ZdgProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zdg:process {game} {--channel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to the zero dollar game.';

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

        $session = new ZdgSession($discord, [
            'channel' => $this->option('channel'),
            'game' => $this->argument('game'),
        ]);

        $session->start();
    }
}
