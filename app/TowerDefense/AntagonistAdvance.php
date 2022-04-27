<?php

namespace App\TowerDefense\Actions\Fillers;

use App\TowerDefense\Actions\ActionCollection;
use App\TowerDefense\Actions\AntagonistAttackAction;
use App\TowerDefense\Actions\AntagonistMoveAction;
use App\TowerDefense\Actions\AntagonistTargetAction;
use App\TowerDefense\Contracts\EventsDirector;
use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Game;
use App\TowerDefense\Models\Tower;
use App\TowerDefense\Pathfinding\PathfindingAStar;
use App\TowerDefense\Pathfinding\PathNode;
use Illuminate\Support\Arr;
use JMGQ\AStar\AStar;

class AntagonistFiller
{
    /**
     * The game instance.
     *
     * @var Game
     */
    protected $game;

    /**
     * The events director.
     *
     * @var EventsDirector
     */
    protected $events;

    public function __construct(Game $game, EventsDirector $events)
    {
        $this->game = $game;
        $this->events = $events;
    }

    /**
     * Advance the antagonists.
     *
     * @return void
     */
    public function advance()
    {
        $antagonists = $this->game->antagonists->sortBy('id');

        foreach ($antagonists as $antagonist) {
            $pathfinder = $this->createPathFinder($this->game);

            $this->attackOrMoveAntagonist($pathfinder, $antagonist);
        }
    }

    /**
     * Determine the antagonist target.
     *
     * @param AStar $pathfinder
     * @param Antagonist $antagonist
     * @return AntagonistTargetAction|null
     */
    protected function attackOrMoveAntagonist(AStar $pathfinder, Antagonist $antagonist)
    {
        $towers = $this->game->towers;
        $antagonistNode = PathNode::fromCoords($antagonist->x, $antagonist->y);
        $closestTower = null;
        $closestDistanceToTower = INF;

        foreach ($towers as $tower) {
            $distance = $this->distanceToTower($tower, $antagonist);

            if ($distance < $closestDistanceToTower) {
                $closestDistanceToTower = $distance;
                $closestTower = $tower;
            }
        }

        $distanceToBase = $this->distanceToBase($this->game, $antagonist);

        $towerPath = [];

        if ($closestTower) {
            $towerPoint = PathNode::fromCoords($closestTower->x, $closestTower->y);
            $towerPath = $pathfinder->run(
                $antagonistNode,
                $towerPoint
            );
        }

        $basePoint = PathNode::fromCoords($this->game->base_x, $this->game->base_y);
        $basePath = $pathfinder->run(
            $antagonistNode,
            $basePoint
        );

        if (
            (
                $closestDistanceToTower / $distanceToBase < config('tower-defense.tower_prioritize_ratio')
                || count($basePath) === 0
            ) && count($towerPath) > 0
        ) {
            if ($this->canAttack($antagonist, $closestTower->x, $closestTower->y)) {
                $this->attackTower($antagonist, $closestTower);
            } else {
                $this->moveToTarget($antagonist, $towerPath);
            }
        } else if (
            count($towerPath) === 0
            && count($basePath) > 0
        ) {
            if ($this->canAttack($antagonist, $this->game->base_x, $this->game->base_y)) {
                $this->attackBase($antagonist);
            } else {
                $this->moveToTarget($antagonist, $basePath);
            }
        }
    }

    /**
     * Check whether the antagonist is within attack range.
     *
     * @param Antagonist $antagonist
     * @param int $x
     * @param int $y
     * @return boolean
     */
    protected function canAttack(Antagonist $antagonist, $x, $y)
    {
        $range = config('tower-defense.antagonist_base_range');

        return amsterdam_distance($antagonist->x, $antagonist->y, $x, $y) <= $range;
    }

    /**
     * Attack the tower.
     *
     * @param Antagonist $antagonist
     * @param Tower $tower
     * @return void
     */
    protected function attackTower(Antagonist $antagonist, Tower $tower)
    {
        $damage = config('tower-defense.antagonist_base_damage');

        $this->events->antagonistIsAttacking($antagonist, $tower);

        $tower->health -= $damage;
        $tower->save();
    }

    /**
     * Attack the base.
     *
     * @param Antagonist $antagonist
     * @return void
     */
    protected function attackBase(Antagonist $antagonist)
    {
        $damage = config('tower-defense.antagonist_base_damage');

        $this->events->antagonistIsAttacking($antagonist, null, true);

        $this->game->base_health -= $damage;
        $this->game->save();
    }

    /**
     * Move the antagonist to the target.
     *
     * @param Antagonist $antagonist
     * @param PathNode[] $path
     * @return void
     */
    protected function moveToTarget(Antagonist $antagonist, $path)
    {
        $speed = config('tower-defense.antagonist_base_speed');

        $node = $path[$speed] ?? Arr::last($path);

        $moveX = $node->getX();
        $moveY = $node->getY();

        $prevX = $antagonist->x;
        $prevY = $antagonist->y;

        $antagonist->x = $moveX;
        $antagonist->y = $moveY;

        $antagonist->save();

        $this->events->antagonistHasMoved($antagonist, $prevX, $prevY);
    }

    /**
     * Calculate the distance to the tower.
     *
     * @param Tower $tower
     * @param Antagonist $antagonist
     * @return int
     */
    protected function distanceToTower(Tower $tower, Antagonist $antagonist)
    {
        $towerX = $tower->x;
        $towerY = $tower->y;

        $antagonistX = $antagonist->x;
        $antagonistY = $antagonist->y;

        return amsterdam_distance($towerX, $towerY, $antagonistX, $antagonistY);
    }

    /**
     * Calculate the distance to the base.
     *
     * @param Game $game
     * @param Antagonist $antagonist
     * @return int
     */
    protected function distanceToBase(Game $game, Antagonist $antagonist)
    {
        $antagonistX = $antagonist->x;
        $antagonistY = $antagonist->y;

        $baseWidth = config('tower-defense.base_width');
        $baseHeight = config('tower-defense.base_height');

        $distance = INF;

        for ($y = $game->base_y; $y < $game->base_y + $baseHeight; $y += 1) {
            for ($x = $game->base_x; $x < $game->base_x + $baseWidth; $x += 1) {
                $distance = min(amsterdam_distance($antagonistX, $antagonistY, $x, $y), $distance);
            }
        }

        return $distance;
    }

    /**
     * Create a new pathfinder.
     *
     * @param Game $game
     * @return PathfindingAStar
     */
    protected function createPathFinder(Game $game)
    {
        return new PathfindingAStar($game);
    }
}
