<?php

namespace App\TowerDefense;

use App\Discord\DiscordSession;
use App\TowerDefense\Models\Game;
use App\TowerDefense\Models\Player;
use App\TowerDefense\Models\Tower;
use Discord\Helpers\Deferred;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;
use Discord\WebSockets\Event;
use React\Promise\ExtendedPromiseInterface;

class GameSession extends DiscordSession
{
    /**
     * A game instance.
     *
     * @var Game
     */
    protected $game;

    /**
     * A player instance.
     *
     * @var Player
     */
    protected $player;

    /**
     * Construct a new game session.
     *
     * @param Game $game
     * @param Player $player
     * @param Tower $tower
     */
    public function __construct($game, Player $player, Tower $tower)
    {
        $this->game = $game;
        $this->player = $player;
    }

    /**
     * @inheritdoc
     */
    protected function initialized()
    {
        $this->discord->on(Event::MESSAGE_CREATE, $this->callback('handleMessage'));

        $this->messageInstructions();
    }

    /**
     * Handle a new discord message.
     *
     * @param Message $discordMessage
     * @return void
     */
    protected function handleMessage(Message $message)
    {
        if ($message->channel_id != $this->game->channel_id) {
            return;
        }

        if (preg_match('/(place +)?tower +(at +)?(?<x>\d+) +(?<y>\d+)/', $message->content, $matches)) {
            $x = $matches['x'];
            $y = $matches['y'];

            $author = $message->author;

            if ($author instanceof Member) {
                $author = $author->user;
            }

            if ($this->validateCoordinates($x, $y)) {
                $this->addTowerAndPlayer($author, $x, $y);
            }
        }
    }

    protected function validateCoordinates($x, $y)
    {
        return true;
    }

    /**
     * Add the tower to the given coordinates.
     *
     * @param User $user
     * @param int $x
     * @param int $y
     * @return void
     */
    protected function addTowerAndPlayer($user, $x, $y)
    {
        $this->addPlayerIfNotPresent($user)->done($this->callback('addTower', $user, $x, $y));
    }

    /**
     * Add the tower.
     *
     * @param User $user
     * @param int $x
     * @param int $y
     * @return void
     */
    protected function addTower($user, $x, $y)
    {

    }

    /**
     * Add the given player if not already added.
     *
     * @param User $user
     * @return ExtendedPromiseInterface
     */
    protected function addPlayerIfNotPresent($user)
    {
        $playerExists = $this->player
            ->newQuery()
            ->where('tdg_id', $this->game->getKey())
            ->where('user_id', $user->id)
            ->exists();

        if ($playerExists) {
            $deferred = (new Deferred());
            $deferred->resolve();

            return $deferred->promise();
        }

        $money = config('tower-defense.default_player_money');

        $this->player->newQuery()->insert([
            'tdg_id' => $this->game->getKey(),
            'user_id' => $user->id,
            'money' => $money,
            'score' => 0,
        ]);

        $channel = $this->discord->getChannel($this->game->channel_id);

        return $channel->sendMessage(
            'You have been added to the game.',
        );
    }

    /**
     * Message the game instructions.
     *
     * @return void
     */
    protected function messageInstructions()
    {
        $channel = $this->discord->getChannel($this->game->channel_id);

        return $channel->sendMessage(
            '<Instructions go here>',
        );
    }
}
