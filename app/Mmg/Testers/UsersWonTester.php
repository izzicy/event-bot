<?php

namespace App\Mmg\Testers;

use App\Mmg\Contracts\TesterInterface;
use App\Services\Users\UserInterface;

class UsersWonTester implements TesterInterface
{
    /** @var bool */
    protected $allTaken = true;

    /** @var UserScoreTester */
    protected $userScoreTester;

    /**
     * Construct a new users won tester.
     */
    public function __construct()
    {
        $this->userScoreTester = new UserScoreTester;
    }

    /** @inheritDoc */
    public function testTile($tile)
    {
        $state = $tile->getState();
        $conquerer = $tile->getConquerer();

        if ($conquerer === null && $state === 'empty' || $state === 'unknown') {
            $this->allTaken = false;
        }

        $this->userScoreTester->testTile($tile);
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
        return collect($this->userScoreTester->getUsers())->sortByDesc(function(UserInterface $user) {
            return $this->userScoreTester->getScoreCount($user);
        });
    }

    /**
     * Get the score count of the given user.
     *
     * @param UserInterface $user
     * @return int
     */
    public function getScoreCount($user)
    {
        return $this->userScoreTester->getScoreCount($user);
    }
}
