<?php

namespace App\Mmg\Contracts;

interface DrawInterface
{
    /**
     * Draw from the game.
     *
     * @param GameInterface $game
     * @return resource
     */
    public function draw($game);
}
