<?php

namespace App\Mmg\Contracts;

interface GameOperatorInterface
{
    /**
     * Operate on the game instance.
     *
     * @param GameInterface $game
     * @return void
     */
    public function operateGame($game);
}
