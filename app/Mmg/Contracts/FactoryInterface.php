<?php

namespace App\Mmg\Contracts;

use App\Services\Users\UserInterface;

interface FactoryInterface
{
    /**
     * Create a mine distributer.
     *
     * @param array[]int[] $picked
     * @param int $mineCount
     * @return GameOperatorInterface
     */
    public function createMineDistributer($picked, $mineCount): GameOperatorInterface;

    /**
     * Create the conquerer.
     *
     * @param array[]array[]int[] $picked
     * @param UserInterface[] $users
     * @return GameOperatorInterface
     */
    public function createConquerer($picked, $users): GameOperatorInterface;
}
