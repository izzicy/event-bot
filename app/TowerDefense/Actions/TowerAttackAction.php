<?php

namespace App\TowerDefense\Actions;

use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Tower;

class TowerAttackAction
{
    /**
     * The tower who's attacking.
     *
     * @var Tower
     */
    public $attackingTower;

    /**
     * The antagonist that is under attack.
     *
     * @var Antagonist
     */
    public $antagonistUnderAttack;
}
