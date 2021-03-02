<?php

namespace App\Services\MMGame\Contracts;

use App\Services\Users\UserInterface;

interface PickableRepositoryInterface
{
    /**
     * Check if the given tile is pickable.
     *
     * @param UserInterface $user
     * @param int $tileX
     * @param int $tileY
     * @return boolean
     */
    public function isPickable($tileX, $tileY, ?UserInterface $user);
}
