<?php

namespace App\TowerDefense\Actions\Fillers;

use App\TowerDefense\Actions\ActionCollection;
use App\TowerDefense\Actions\AntagonistAttackAction;
use App\TowerDefense\Actions\AntagonistMoveAction;
use App\TowerDefense\Actions\AntagonistTargetAction;
use App\TowerDefense\Contracts\ActionCollectionFiller;
use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Game;
use App\TowerDefense\Models\Tower;
use App\TowerDefense\Pathfinding\PathfindingAStar;
use App\TowerDefense\Pathfinding\PathNode;
use Illuminate\Support\Arr;
use JMGQ\AStar\AStar;

class AntagonistFiller implements ActionCollectionFiller
{
    /**
     * The game instance.
     *
     * @var Game
     */
    protected $game;

    /**
     * Construct a new filler.
     *
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * @inheritdoc
     */
    public function fill(ActionCollection $actions): ActionCollection
    {
        $antagonists = $this->game->antagonists->sortBy('id');
        $range = config('tower-defense.antagonist_base_range');

        foreach ($antagonists as $antagonist) {
            $pathfinder = $this->createPathFinder($this->game, $actions);
            $targetAction = $this->createTargetAction($pathfinder, $this->game, $antagonist);

            if ($targetAction) {
                $actions->antagonistTargets[] = $targetAction;
                $distance = amsterdam_distance($antagonist->x, $antagonist->y, $targetAction->x, $targetAction->y);

                if ($distance <= $range) {
                    $actions->antagonistAttacks[] = $this->createAttackAction($targetAction);
                } else {
                    $actions->antagonistMoves[] = $this->createAttackAction($targetAction);
                }
            }
        }

        return $actions;
    }

    /**
     * Create an attack action.
     *
     * @param AntagonistTargetAction $targetAction
     * @return AntagonistAttackAction
     */
    protected function createAttackAction(AntagonistTargetAction $targetAction)
    {
        $action = new AntagonistAttackAction;

        $action->attackingAntagonist = $targetAction->targetingAntagonist;
        $action->isAttackingBase = $targetAction->targetedAtBase;
        $action->towerUnderAttack = $targetAction->targetedTower;

        return $action;
    }

    /**
     * Create an antagonist move action.
     *
     * @param AntagonistTargetAction $targetAction
     * @return AntagonistMoveAction
     */
    protected function createMoveAction(AntagonistTargetAction $targetAction)
    {
        $action = new AntagonistMoveAction;
        $speed = config('tower-defense.antagonist_base_speed');

        $action->movingAntagonist = $targetAction->targetingAntagonist;

        $previousNode = $targetAction->path[$speed - 1] ?? $targetAction->path[count($targetAction->path) - 2];
        $node = $targetAction->path[$speed] ?? Arr::last($targetAction->path);

        $deltaX = $node->getX() - $previousNode->getX();
        $deltaY = $node->getY() - $previousNode->getY();

        $action->x = $node->getX();
        $action->y = $node->getY();

        $action->direction = align_to_compass($deltaX, $deltaY);

        return $action;
    }

    /**
     * Create the target action.
     *
     * @param AStar $pathfinder
     * @param Game $game
     * @param Antagonist $antagonist
     * @return AntagonistTargetAction|null
     */
    protected function createTargetAction(AStar $pathfinder, Game $game, Antagonist $antagonist)
    {
        $towers = $game->towers;
        $antagonistNode = PathNode::fromCoords($antagonist->x, $antagonist->y);
        $action = new AntagonistTargetAction;

        $action->targetingAntagonist = $antagonist;

        $closestTower = null;
        $closestDistanceToTower = INF;

        foreach ($towers as $tower) {
            $distance = $this->distanceToTower($tower, $antagonist);

            if ($distance < $closestDistanceToTower) {
                $closestDistanceToTower = $distance;
                $closestTower = $tower;
            }
        }

        $distanceToBase = $this->distanceToBase($game, $antagonist);

        $towerPath = [];

        if ($closestTower) {
            $towerPoint = PathNode::fromCoords($closestTower->x, $closestTower->y);
            $towerPath = $pathfinder->run(
                $antagonistNode,
                $towerPoint
            );
        }

        $basePoint = PathNode::fromCoords($game->base_x, $game->base_y);
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
            $action->targetedAtBase = false;
            $action->targetedTower = $closestTower;
            $action->x = $closestTower->x;
            $action->y = $closestTower->y;
            $action->path = $towerPath;
        } else if (
            count($towerPath) === 0
            && count($basePath) > 0
        ) {
            $action->targetedAtBase = true;
            $action->x = $game->base_x;
            $action->y = $game->base_y;
            $action->path = $basePath;
        } else {
            return null;
        }

        return $action;
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
     * @param ActionCollection $action
     * @return PathfindingAStar
     */
    protected function createPathFinder(Game $game, ActionCollection $actions)
    {
        return new PathfindingAStar($game, $actions);
    }
}
