<?php

namespace App\Services\MMGame\Contracts;

use App\Services\Users\UserInterface;

interface UserAssocTilesCollectionInterface
{
    /**
     * Check if a tile exists at.
     *
     * @param int $x
     * @param int $y
     * @return boolean
     */
    public function hasTileAt($x, $y): bool;

    /**
     * Check if a tile with the given user exists at.
     *
     * @param int $x
     * @param int $y
     * @param UserInterface $user
     * @return boolean
     */
    public function hasUserTileAt($x, $y, UserInterface $user): bool;

    /**
     * Get the tile at the coordinates.
     *
     * @param int $x
     * @param int $y
     * @return UserAssociatedTileInterface|null
     */
    public function getTileAt($x, $y): ?UserAssociatedTileInterface;

    /**
     * Get all associated tiles.
     *
     * @return UserAssociatedTileInterface[]
     */
    public function all();
}
