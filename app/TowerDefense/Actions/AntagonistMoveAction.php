<?php

namespace App\TowerDefense\Actions;

use App\TowerDefense\Models\Antagonist;

class AntagonistMoveAction
{
    /**
     * The antagonist that is moving.
     *
     * @var Antagonist
     */
    public $movingAntagonist;

    /**
     * The x coordinate to which the antagonist is moving.
     *
     * @var int
     */
    public $x;

    /**
     * The y coordinate to which the antagonist is moving.
     *
     * @var int
     */
    public $y;
}
