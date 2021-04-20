<?php

namespace App\Mmg\Commands;

use App\Mmg\Contracts\GameOperatorInterface;
use App\Mmg\Contracts\MessageHandlerInterface;
use App\Services\Users\UserInterface;

class UnflagCommand implements MessageHandlerInterface, GameOperatorInterface
{
    /**
     * An associated array with coordinate picks and user ids as the key.
     *
     * @var array[]array[]int[]
     */
    protected $flags = [];

    /**
     * An associative array of users.
     *
     * @var UserInterface[]
     */
    protected $users = [];

    /**
     * The maximum number of picks allowed.
     *
     * @var int
     */
    protected $maxPicks = INF;

    /**
     * Pick tile command constructor.
     *
     * @param int $mineCount
     */
    public function __construct($mineCount)
    {
        $this->mineCount = $mineCount;
    }

    /** @inheritdoc */
    public function handleMessage($message)
    {
        $user = $message->getUser();

        if (preg_match_all('/(unflag|unmark)\s+(?P<x>\d+)(,|\s)+(?P<y>\d+)/', $message->getMessage(), $matches)) {
            if (empty($this->flags[$user->getId()])) {
                $this->users[] = $user;
            }

            foreach ($matches['x'] as $key => $x) {
                $y = $matches['y'][$key] ?? 0;

                $this->flags[$user->getId()][] = [$x, $y];
            }
        }
    }

    /** @inheritdoc */
    public function operateGame($game)
    {
        foreach ($this->users as $user) {
            $userId = $user->getId();

            foreach ($this->flags[$userId] as $pick) {
                if ($game->hasTileAt($pick[0], $pick[1]) === false) {
                    continue;
                }

                $tile = $game->getTileAt($pick[0], $pick[1]);

                $tile->removeFlagger($user);
            }
        }
    }
}
