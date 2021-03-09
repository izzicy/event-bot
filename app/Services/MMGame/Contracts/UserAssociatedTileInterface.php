<?php

namespace App\Services\MMGame\Contracts;

use App\Services\Users\UserInterface;

interface UserAssociatedTileInterface
{
    /**
     * Get the user associated with this tile.
     *
     * @return UserInterface
     */
    public function getUser(): UserInterface;

    /**
     * Get the x coordinate of the tile.
     *
     * @return int
     */
    public function getX();

    /**
     * Get the y coordinate of the tile.
     *
     * @return int
     */
    public function getY();
}
