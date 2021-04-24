<?php

namespace App\Mmg\Testers;

use App\Mmg\Contracts\TesterInterface;
use App\Services\Users\UserInterface;

class UserFlagTester implements TesterInterface
{
    /** @var array */
    protected $countsPerUser = [];

    /** @var UserInterface[] */
    protected $users = [];

    /** @inheritDoc */
    public function testTile($tile)
    {
        $state = $tile->getState();
        $flaggers = $tile->getFlaggers();

        if ($state === 'empty' || $state === 'unknown') {
            foreach ($flaggers as $flagger) {
                $flaggerId = $flagger->getId();

                if (isset($this->countsPerUser[$flaggerId]) === false) {
                    $this->users[] = $flagger;
                    $this->countsPerUser[$flaggerId] = 0;
                }

                $this->countsPerUser[$flaggerId] += 1;
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
     * Get the flag count.
     *
     * @param UserInterface $user
     * @return int
     */
    public function getFlagCount($user)
    {
        return $this->countsPerUser[$user->getId()] ?? 0;
    }
}
