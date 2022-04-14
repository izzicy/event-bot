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

    /**
     * Handle a discord message.
     *
     * @param Message $message
     * @return void
     */
    protected function handleMessage(Message $discordMessage)
    {
        if ($discordMessage->channel_id != $this->channelId) {
            return;
        }

        $messages = app(Factory::class)->createFromDirectResponses([$discordMessage], $this->discord->id);

        $choices = new ChoicesAggregate();
        $pixelColourer = new PixelColourerCommand($choices);
        $fromImage = new FromImageColourerCommand($choices);

        foreach ($messages as $message) {
            $pixelColourer->handleMessage($message);
            $fromImage->handleMessage($message);
        }

        if ($choices->hasChoices()) {
            $choices->operateGame($this->game);

            $this->drawAndSendClearGame()->done(function() {
                $this->drawAndSendGridGame();
            });
        }
    }

    /**
     * Draw and send the clear game.
     *
     * @return ExtendedPromiseInterface
     */
    protected function drawAndSendClearGame()
    {
        $drawer = new DrawGame();

        $this->game->refresh();

        $channel = $this->discord->getChannel($this->channelId);
        $image = ImageManagerStatic::make($drawer->draw($this->game));
        $path = tempnam(sys_get_temp_dir(), '') . '.png';
        $image->save($path);

        return $channel->sendFile($path);
    }

    /**
     * Draw and send the grid game.
     *
     * @return ExtendedPromiseInterface
     */
    protected function drawAndSendGridGame()
    {
        $drawer = new DrawGame();

        $this->game->refresh();

        $channel = $this->discord->getChannel($this->channelId);
        $image = ImageManagerStatic::make($drawer->draw($this->game, true));
        $path = tempnam(sys_get_temp_dir(), '') . '.png';
        $image->save($path);

        return $channel->sendFile($path);
    }
}
