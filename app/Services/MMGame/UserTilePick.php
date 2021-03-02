<?php

namespace App\Services\MMGame;

use App\Services\Users\UserInterface;

class UserTilePick
{
    protected $user;
    protected $tileX;
    protected $tileY;

    /**
     * Construct a new user tile pick.
     *
     * @param UserInterface $user
     * @param int $tileX
     * @param int $tileY
     */
    public function __construct(UserInterface $user, $tileX, $tileY)
    {
        $this->user = $user;
        $this->tileX = $tileX;
        $this->tileY = $tileY;
    }

    /**
     * Get the user.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the x coord of the pick.
     *
     * @return int
     */
    public function getX()
    {
        return $this->tileX;
    }

    /**
     * Get the y coord of the pick.
     *
     * @return int
     */
    public function getY()
    {
        return $this->tileY;
    }
}
