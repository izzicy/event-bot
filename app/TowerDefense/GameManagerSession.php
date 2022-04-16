<?php

namespace App\TowerDefense;

use App\Discord\DiscordSession;
use App\TowerDefense\Models\Game;
use App\TowerDefense\GameSession;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;

class GameManagerSession extends DiscordSession
{
    /**
     * The event dispatcher.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * A game instance.
     *
     * @var Game
     */
    protected $game;

    /**
     * The channel id in which new games can start.
     *
     * @var string
     */
    protected $channelId;

    /**
     * All of the active tower defense games.
     *
     * @var Collection
     */
    protected $games;

    /**
     * An associative array of game sessions.
     *
     * @var Collection
     */
    protected $gameSessions;

    /**
     * Construct a new tower defense game.
     *
     * @param Dispatcher $dispatcher
     * @param Game $game
     * @param string $channelId
     */
    public function __construct(Dispatcher $dispatcher, Game $game, $channelId)
    {
        $this->dispatcher = $dispatcher;
        $this->game = $game;
        $this->games = $this->game->newQuery()->where('state', Game::STATE_PLAYING)->get();
        $this->channelId = $channelId;
    }

    /**
     * @inheritdoc
     */
    protected function initialized()
    {
        $this->discord->on(Event::MESSAGE_CREATE, $this->callback('handleMessage'));

        foreach ($this->games as $game) {
            $this->createGameSession($game);
        }
    }

    /**
     * Handle the message.
     *
     * @param Message $message
     * @return void
     */
    protected function handleMessage(Message $message)
    {
        if ($message->channel_id != $this->channelId) {
            return;
        }

        if (preg_match('/start +an? +new +(tower +defense +)?game/', $message->content)) {
            $channel = $this->discord->getChannel($message->channel_id);

            $gameExists = $this->games->contains(function($game) use ($message) {
                return $game->channel_id == $message->channel_id;
            });

            if ($gameExists) {
                $channel->sendMessage(
                    sprintf('A game is already being played in %s', $channel->name)
                );

                return;
            }

            $game = $this->game->newQuery()->create([
                'channel_id' => $this->channelId,
                'state' => Game::STATE_PLAYING,
                'base_health' => config('tower-defense.tower_base_health'),
                'base_x' => 5,
                'base_y' => 5,
                'width' => 10,
                'height' => 10,
            ]);

            $this->games->push($game);

            $this->createGameSession($game);

            $channel->sendMessage(
                sprintf('A new game has started in %s', $channel->name)
            );
        }
    }

    /**
     * Create a session for the given game.
     *
     * @param Game $game
     * @return GameSession
     */
    protected function createGameSession($game)
    {
        $session = $this->createSession(GameSession::class, [
            'game' => $game,
        ]);

        $session->start();

        $this->gameSessions[$game->getKey()] = $session;

        return $session;
    }
}
