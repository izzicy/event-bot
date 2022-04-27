<?php

namespace App\TowerDefense\View;

use App\TowerDefense\Contracts\EventsDirector;
use App\TowerDefense\Models\Game;

class AreaData implements EventsDirector
{
    /**
     * The game model instance.
     *
     * @var Game
     */
    protected $game;

    /**
     * The new data per antagonist.
     *
     * @var array
     */
    protected $dataPerAntagonist = [];

    /**
     * Construct a new area data.
     *
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Get the game width.
     *
     * @return int
     */
    public function width()
    {
        return $this->game->width;
    }

    /**
     * Get the game height.
     *
     * @return int
     */
    public function height()
    {
        return $this->game->height;
    }

    /**
     * Get the antagonists.
     *
     * @return AntagonistData[]
     */
    public function antagonists()
    {
        return $this->game->antagonists->map(function($antagonist) {
            return new AntagonistData($antagonist, $this->dataPerAntagonist[$antagonist->getKey()]);
        });
    }

    /**
     * @inheritdoc
     */
    public function antagonistIsAttacking($antagonist, $tower = null, $isAttackingBase = false)
    {
        $antagonistId = $antagonist->getKey();
        $deltaX = 0;
        $deltaY = 0;

        if ($tower) {
            $deltaX = $tower->x - $antagonist->x;
            $deltaY = $tower->y - $antagonist->y;
        } else if ($isAttackingBase) {
            $deltaX = $this->game->base_x - $antagonist->x;
            $deltaY = $this->game->base_y - $antagonist->y;
        }

        $this->dataPerAntagonist[$antagonistId]['isAttacking'] = true;
        $this->dataPerAntagonist[$antagonistId]['facing'] = align_to_compass($deltaX, $deltaY);
    }

    /**
     * @inheritdoc
     */
    public function antagonistHasMoved($antagonist, $prevX, $prevY)
    {
        $antagonistId = $antagonist->getKey();
        $deltaX = $prevX - $antagonist->x;
        $deltaY = $prevY - $antagonist->y;

        $this->dataPerAntagonist[$antagonistId]['isMoving'] = true;
        $this->dataPerAntagonist[$antagonistId]['facing'] = align_to_compass($deltaX, $deltaY);
    }

    /**
     * @inheritdoc
     */
    public function towerIsAttacking($tower, $antagonist)
    {
        // no-op
    }

    /**
     * @inheritdoc
     */
    public function towerIsBuild($tower)
    {
        // no-op
    }
}
