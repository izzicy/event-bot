<?php

namespace App\Mmg\Testers;

use App\Mmg\Contracts\TesterInterface;
use App\Services\Users\UserInterface;

class UsersWonTester implements TesterInterface
{
    /** @var bool */
    protected $allTaken = true;

    /** @var array */
    protected $countsPerUser = [];

    /** @var UserInterface[] */
    protected $users = [];

    /** @inheritDoc */
    public function testTile($tile)
    {
        $state = $tile->getState();
        $conquerer = $tile->getConquerer();

        if ($conquerer === null && $state === 'empty' || $state === 'unknown') {
            $this->allTaken = false;
        }

        if ($conquerer !== null) {
            $conquererId = $conquerer->getId();

            if (empty($this->countsPerUser[$conquererId])) {
                $this->users[] = $conquerer;
                $this->countsPerUser[$conquererId] = 0;
            }

            $this->countsPerUser[$conquererId] += 1;
        }
    }

    /**
     * Check whether all tiles are taken.
     *
     * @return bool
     */
    public function areAllTilesTaken()
    {
        return $this->allTaken;
    }

    /**
     * Get the users in order.
     *
     * @return UserInterface[]
     */
    public function getUsersInOrder()
    {
        return collect($this->users)->sortByDesc(function(UserInterface $user) {
            return $this->countsPerUser[$user->getId()];
        });
    }
}
