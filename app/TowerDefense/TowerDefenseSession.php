<?php

namespace App\Zdg;

use App\Discord\DiscordSession;
use App\Services\Messages\Factory;
use App\Zdg\Draw\DrawGame;
use App\Zdg\Models\Game;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Illuminate\Support\Arr;
use Intervention\Image\ImageManagerStatic;
use React\Promise\ExtendedPromiseInterface;

class ZdgSession extends DiscordSession
{
    protected $channelId;

    /**
     * @inheritdoc
     */
    public function start()
    {
        $channelId = $this->option('channel') ?? config('zdg.default-channel');

        $game = Game::find($this->option('game'));

        $this->channelId = $channelId;
        $this->game = $game;

        $this->initialize();
    }

    /**
     * @inheritdoc
     */
    protected function initialized()
    {
        $this->discord->on(Event::MESSAGE_CREATE, $this->callback('handleMessage'));
    }

    protected function handleMessage()
    {

    }
}
