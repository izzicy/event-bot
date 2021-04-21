<?php

namespace App\Mmg;

use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\Contracts\GameOperatorInterface;

class Factory implements FactoryInterface
{
    /** @inheritDoc */
    public function createMineDistributer($picked, $mineCount): GameOperatorInterface
    {
        return new MineDistributer($picked, $mineCount);
    }

    /** @inheritDoc */
    public function createConquerer($picked, $users): GameOperatorInterface
    {
        return new Conquerer($picked, $users);
    }
}
