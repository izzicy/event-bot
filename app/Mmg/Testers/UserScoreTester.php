<?php

namespace App\Mmg\Testers;

use App\Mmg\Contracts\TesterInterface;
use App\Services\Users\UserInterface;

class UserScoreTester implements TesterInterface
{
    /** @var array */
    protected $countsPerUser = [];

    /** @var UserInterface[] */
    protected $users = [];

    /** @inheritDoc */
    public function testTile($tile)
    {
        $state = $tile->getState();
        $conquerer = $tile->getConquerer();

        if ($conquerer !== null) {
            $conquererId = $conquerer->getId();

            if (isset($this->countsPerUser[$conquererId]) === false) {
                $this->users[] = $conquerer;
                $this->countsPerUser[$conquererId] = 0;
            }

            if ($state === 'empty' || $state === 'unknown') {
                $this->countsPerUser[$conquererId] += 1;
            }

            if ($state === 'mine') {
                $this->countsPerUser[$conquererId] -= config('mmg.score-penalty');
            }
        }
    }

    /**
     * Get the users in order.
     *
     * @return UserInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Get the score count.
     *
     * @param UserInterface $user
     * @return int
     */
    public function getScoreCount($user)
    {
        return $this->countsPerUser[$user->getId()] ?? 0;
    }
}
