<?php

namespace App\TowerDefense\Actions;

use App\TowerDefense\Models\Antagonist;

class AntagonistAttackAction
{
    /**
     * The antagonist that's being spawned. Null when the antagonist is yet to be spawned.
     *
     * @var Antagonist|null
     */
    public $spawningAntagonist;

    /**
     * The x spawn coordinate of the antagonist.
     *
     * @var int
     */
    public $x;

    /**
     * The y spawn coordinate of the antagonist.
     *
     * @var int
     */
    public $y;
}
