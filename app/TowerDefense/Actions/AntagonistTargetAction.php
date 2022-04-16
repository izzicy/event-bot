<?php

namespace App\TowerDefense\Actions;

use App\TowerDefense\Models\Antagonist;

class AntagonistTargetAction
{
    /**
     * The antagonist that is targeting.
     *
     * @var Antagonist
     */
    public $targetingAntagonist;

    /**
     * The x coordinate to which the antagonist is targeting.
     *
     * @var int
     */
    public $x;

    /**
     * The y coordinate to which the antagonist is targeting.
     *
     * @var int
     */
    public $y;
}
