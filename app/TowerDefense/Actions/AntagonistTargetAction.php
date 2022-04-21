<?php

namespace App\TowerDefense\Actions;

use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Tower;
use App\TowerDefense\Pathfinding\PathNode;

class AntagonistTargetAction
{
    /**
     * The antagonist that is targeting.
     *
     * @var Antagonist
     */
    public $targetingAntagonist;

    /**
     * The tower that is being targeted, if one exists.
     *
     * @var Tower|null
     */
    public $targetedTower;

    /**
     * Whether the antagonist is targeting the base.
     *
     * @var boolean
     */
    public $targetedAtBase;

    /**
     * The path towards the target.
     *
     * @var PathNode[]
     */
    public $path;

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
