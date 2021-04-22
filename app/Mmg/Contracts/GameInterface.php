<?php

namespace App\Mmg\Contracts;

interface GameInterface
{
    /**
     * Get the game width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get the game height.
     *
     * @return int
     */
    public function getHeight();

    /**
     * Check whether the game has initialized.
     *
     * @return boolean
     */
    public function hasInitialized();

    /**
     * Get the mine count.
     *
     * @return int
     */
    public function getMineCount();

    /**
     * Set the initialized status to true.
     *
     * @return void
     */
    public function initialize();

    /**
     * Check whether a tile exists at.
     *
     * @param int $x
     * @param int $y
     * @return boolean
     */
    public function hasTileAt($x, $y);

    /**
     * Get the tile at..
     *
     * @param int $x
     * @param int $y
     * @return TileInterface|null
     */
    public function getTileAt($x, $y);

    /**
     * Get all tiles of this game.
     *
     * @return TileInterface[]
     */
    public function getTiles();
}
