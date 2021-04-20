<?php

namespace App\Mmg\Contracts;

use App\Services\Users\UserInterface;

interface TileInterface
{
    /**
     * Get the game.
     *
     * @return GameInterface
     */
    public function getGame();

    /**
     * The tile state.
     * Either 'mine', 'empty' or 'unknown'.
     *
     * @return string
     */
    public function getState();

    /**
     * Set the state.
     *
     * @param string $state
     * @return void
     */
    public function setState($state);

    /**
     * Get the nearby mine count.
     *
     * @return int
     */
    public function getNearbyMineCount();

    /**
     * Get the tile conquerer.
     *
     * @return UserInterface|null
     */
    public function getConquerer();

    /**
     * Set the tile conquerer.
     *
     * @param UserInterface|null $user
     * @return void
     */
    public function setConquerer($user);

    /**
     * Get the tile flaggers.
     *
     * @return UserInterface[]
     */
    public function getFlaggers();

    /**
     * Add a flagger.
     *
     * @param UserInterface $user
     * @return void
     */
    public function addFlagger($user);

    /**
     * Remove a flagger.
     *
     * @param UserInterface $user
     * @return void
     */
    public function removeFlagger($user);
}
