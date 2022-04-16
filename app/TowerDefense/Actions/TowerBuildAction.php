<?php

namespace App\TowerDefense\Actions;

use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Tower;

class TowerBuildAction
{
    /**
     * The tower who's being build. Null when the tower has yet to be build.
     *
     * @var Tower|null
     */
    public $buildTower;

    /**
     * The x coordinate at which the tower is being build.
     *
     * @var int
     */
    public $x;

    /**
     * The y coordinate at which the tower is being build.
     *
     * @var int
     */
    public $y;
}
