<?php

namespace App\Mmg;

use App\Mmg\Contracts\GameInterface;
use App\Mmg\Contracts\GameOperatorInterface;
use App\Services\Users\UserInterface;

class Conquerer implements GameOperatorInterface
{
    /** @var GameInterface */
    protected $game;

    /** @var array[]int[] */
    protected $pickedTiles;

    /** @var UserInterface[] */
    protected $users;

    /**
     * The queues per user.
     *
     * @var
     */
    protected $queues = [];

    /**
     * The discovered tiles.
     *
     * @var array
     */
    protected $discovered = [];

    /**
     * Mine distributer constructor.
     *
     * @param array[]int[] $pickedTiles
     * @param UserInterface[] $users
     */
    public function __construct($pickedTiles, $users)
    {
        $this->pickedTiles = $pickedTiles;
        $this->users = $users;
    }

    /** @inheritDoc */
    public function operateGame($game)
    {
        $this->queues = [];
        $this->discovered = [];
        $this->game = $game;

        $this->initializeQueues();
        $users = $this->users;

        while ($this->someQueuesAreNotEmpty()) {
            foreach ($users as $user) {
                if ($this->isQueueNotEmpty($user)) {
                    list($tileX, $tileY) = $this->dequeue($user);

                    $this->handleNeighbouringTiles($tileX, $tileY, $user);
                }
            }
        }

        foreach ($this->discovered as $x => $rows) {
            foreach ($rows as $y => $user) {
                $tile = $game->getTileAt($x, $y);

                $tile->setConquerer($user);
            }
        }
    }

    /**
     * Initialize the queues.
     *
     * @return void
     */
    protected function initializeQueues()
    {
        foreach ($this->users as $user) {
            $userId = $user->getId();

            foreach ($this->pickedTiles[$userId] as $pick) {
                $this->handleTile($pick[0], $pick[1], $user);
            }
        }
    }

    /**
     * Handle the neighbouring tiles.
     *
     * @param int $tileX
     * @param int $tileY
     * @param UserInterface $user
     * @return void
     */
    protected function handleNeighbouringTiles($tileX, $tileY, UserInterface $user)
    {
        $neighbours = [
            [ $tileX - 1, $tileY ],
            [ $tileX + 1, $tileY ],
            [ $tileX, $tileY + 1 ],
            [ $tileX, $tileY - 1 ],
            [ $tileX - 1, $tileY - 1 ],
            [ $tileX + 1, $tileY - 1 ],
            [ $tileX - 1, $tileY + 1 ],
            [ $tileX + 1, $tileY + 1 ],
        ];

        foreach ($neighbours as $neighbour) {
            $this->handleTile($neighbour[0], $neighbour[1], $user);
        }
    }

    /**
     * Handle the tile.
     *
     * @param int $tileX
     * @param int $tileY
     * @param UserInterface $user
     * @return void
     */
    protected function handleTile($tileX, $tileY, UserInterface $user)
    {
        $tile = $this->game->getTileAt($tileX, $tileY);

        if (
            $tile !== null
            && $this->isDiscovered($tileX, $tileY) === false
            && $tile->getConquerer() == null
        ) {
            if ($tile->getNearbyMineCount() === 0 && $tile->getState() === 'empty') {
                $this->discoverTile($tileX, $tileY, $user);
                $this->enqueue($user, $tileX, $tileY);
            } else {
                $this->discoverTile($tileX, $tileY, $user);
            }
        }
    }

    /**
     * Some queues are not empty.
     *
     * @return bool
     */
    protected function someQueuesAreNotEmpty()
    {
        foreach ($this->queues as $queue) {
            if (empty($queue) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the queue is not empty.
     *
     * @param UserInterface $user
     * @return boolean
     */
    protected function isQueueNotEmpty(UserInterface $user)
    {
        return empty($this->queues[$user->getId()]) === false;
    }

    /**
     * Enqueu the given tile for the given user.
     *
     * @param UserInterface $user
     * @param int $tileX
     * @param int $tileY
     * @return void
     */
    protected function enqueue(UserInterface $user, $tileX, $tileY)
    {
        $this->queues[$user->getId()][] = [$tileX, $tileY];
    }

    /**
     * Dequeue for the given user.
     *
     * @param UserInterface $user
     * @return array
     */
    protected function dequeue(UserInterface $user)
    {
        return array_shift($this->queues[$user->getId()]);
    }

    /**
     * Check whether the given tile is discovered.
     *
     * @param int $tileX
     * @param int $tileY
     * @return boolean
     */
    protected function isDiscovered($tileX, $tileY)
    {
        return isset($this->discovered[$tileX][$tileY]);
    }

    /**
     * Set the tile to 'discovered'.
     *
     * @param int $tileX
     * @param int $tileY
     * @param UserInterface $discoveredBy
     * @return void
     */
    protected function discoverTile($tileX, $tileY, $discoveredBy)
    {
        $this->discovered[$tileX][$tileY] = $discoveredBy;
    }
}
