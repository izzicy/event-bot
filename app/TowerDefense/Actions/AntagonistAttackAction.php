<?php

namespace App\TowerDefense\Actions;

use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Tower;

class AntagonistAttackAction
{
    /**
     * The antagonist who's attacking.
     *
     * @var Antagonist
     */
    public $attackingAntagonist;

    /**
     * Whether the base is under attack
     *
     * @var boolean
     */
    public $isAttackingBase = false;

    /**
     * The tower that is under attack.
     *
     * @var Tower|null
     */
    public $towerUnderAttack;
}
