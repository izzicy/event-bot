<?php

namespace App\Mmg\Testers;

use App\Mmg\Contracts\TesterInterface;
use App\Services\Users\UserInterface;

class UserMovesTester implements TesterInterface
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
                $this->countsPerUser[$conquererId] = config('mmg.default-user-moves');
            }

            if ($state === 'mine') {
                $this->countsPerUser[$conquererId] = max(
                    1,
                    $this->countsPerUser[$conquererId] - config('mmg.move-penalty')
                );
            }
        }
    }

    /**
     * Get the users.
     *
     * @return UserInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Get the moves count.
     *
     * @param UserInterface $user
     * @return int
     */
    public function getNumberOfMoves($user)
    {
        return $this->countsPerUser[$user->getId()] ?? config('mmg.default-user-moves');
    }
}
