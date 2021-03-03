<?php

namespace App\Services\MMGame\Conquerers;

use App\Services\MMGame\UserTilePick;
use App\Services\MMGame\UserTilePicksCollection;
use App\Services\StateGrid\StateGridInterface;
use App\Services\Users\UserInterface;
use Discord\Parts\User\User;

abstract class AbstractConquerer
{
    /**
     * The state grid.
     *
     * @var StateGridInterface
     */
    protected $grid;

    /**
     * The user picks collection.
     *
     * @var UserTilePicksCollection
     */
    protected $picks;

    /**
     * The queues per user.
     *
     * @var
     */
    protected $queues;

    /**
     * The discovered tiles.
     *
     * @var array
     */
    protected $discovered = [];

    /**
     * @param UserTilePicksCollection $picks
     */
    public function __construct(StateGridInterface $grid, UserTilePicksCollection $picks)
    {
        $this->grid = $grid;
        $this->picks = $picks;

        $users = collect();

        foreach ($picks as $pick) {
            /** @var UserTilePick $pick */

            $this->enqueue($pick->getUser(), $pick->getX(), $pick->getY());

            if ( ! $users->has($pick->getUser()->getId())) {
                $users->put($pick->getUser()->getId(), $pick->getUser());
            }
        }

        while ($this->someQueuesAreNotEmpty()) {
            foreach ($users as $user) {
                if ($this->isQueueNotEmpty($user)) {
                    list($listX, $listY) = $this->dequeue($user);


                }
            }
        }
    }

    /**
     * Create the conquers.
     *
     * @param UserTilePicksCollection $picks
     * @return mixed
     */
    // public function createConquers(


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
     * @return void
     */
    protected function dequeue(UserInterface $user)
    {
        return array_pop()
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
        return isset($this->discovered[$tileX . ':' . $tileY]);
    }

    /**
     * Set the tile to 'discovered'.
     *
     * @param int $tileX
     * @param int $tileY
     * @return void
     */
    protected function discoverTile($tileX, $tileY)
    {
        $this->discovered[$tileX . ':' . $tileY] = true;
    }
}
