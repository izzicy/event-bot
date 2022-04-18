<?php

namespace App\TowerDefense\Actions;

class ActionCollection
{
    /**
     * Antagonist attack actions.
     *
     * @var AntagonistAttackAction[]
     */
    public $antagonistAttacks = [];

    /**
     * Antagonist move actions.
     *
     * @var AntagonistMoveAction[]
     */
    public $antagonistMoves = [];

    /**
     * Antagonist spawn actions.
     *
     * @var AntagonistSpawnAction[]
     */
    public $antagonistSpawn = [];

    /**
     * Antagonist target actions.
     *
     * @var AntagonistTargetAction[]
     */
    public $antagonistTargets = [];

    /**
     * Tower attack actions.
     *
     * @var TowerAttackAction[]
     */
    public $towerAttacks = [];

    /**
     * Tower build actions.
     *
     * @var TowerBuildAction[]
     */
    public $towerBuilds = [];
}
