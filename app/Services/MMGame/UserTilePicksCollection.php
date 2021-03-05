<?php

namespace App\Services\MMGame;

use Illuminate\Support\Collection;

class UserTilePicksCollection extends Collection
{
    /**
     * Check if the given coords are picked.
     *
     * @param int $tileX
     * @param int $tileY
     * @return boolean
     */
    public function isPicked($tileX, $tileY)
    {
        foreach ($this as $tilePick) {
            if ($tilePick->getX() === $tileX && $tilePick->getY() === $tileY) {
                return true;
            }
        }

        return false;
    }
}
