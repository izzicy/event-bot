<?php

namespace App\TowerDefense\Pathfinding;

use App\TowerDefense\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use JMGQ\AStar\AStar;
use JMGQ\AStar\Node;

class PathfindingAStar extends AStar
{
    /**
     * A game instance.
     *
     * @var Game
     */
    protected $game;

    /**
     * The towers index.
     *
     * @var array
     */
    protected $towersIndex;

    /**
     * The antagonists index.
     *
     * @var array
     */
    protected $antagonistsIndex;

    /**
     * Construct a new pathfinding a star.
     *
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;

        $this->towersIndex = $this->indexTowers($game->towers);
        $this->antagonistsIndex = $this->indexAntagonists($game->antagonists);
    }

    /**
     * @inheritdoc
     */
    public function generateAdjacentNodes(Node $node)
    {
        $adjacentNodes = [];

        list($nodeX, $nodeY) = explode('x', $node->getID());

        for ($y = $nodeY - 1; $y <= $nodeY + 1; $y += 1) {
            for ($x = $nodeX - 1; $x <= $nodeX + 1; $x += 1) {
                $adjacentNode = PathNode::fromCoords($x, $y);

                if (
                    (
                        $x >= 0 && $y >= 0
                    ) && (
                        $x < $this->game->width && $y < $this->game->height
                    ) && (
                        $adjacentNode->getID() !== $node->getID()
                    )
                ) {
                    $adjacentNodes[] = $adjacentNode;
                }
            }
        }

        return $adjacentNodes;
    }

    /**
     * @inheritdoc
     */
    public function calculateRealCost(Node $node, Node $adjacent)
    {
        $pathNode = PathNode::fromNode($node);
        $adjacentPathNode = PathNode::fromNode(($adjacent));
        $adjacentX = $adjacentPathNode->getX();
        $adjacentY = $adjacentPathNode->getY();

        if ( ! $this->areAdjacent($pathNode, $adjacentPathNode)) {
            return PHP_INT_MAX;
        }

        if (
            isset(
                $this->towersIndex[$adjacentY][$adjacentX]
            )
        ) {
            return PHP_INT_MAX;
        }

        if (
            isset(
                $this->antagonistsIndex[$adjacentX][$adjacentY]
            )
        ) {
            return PHP_INT_MAX;
        }

        return 1;
    }

    /**
     * @inheritdoc
     */
    public function calculateEstimatedCost(Node $start, Node $end)
    {
        $startPathNode = PathNode::fromNode($start);
        $endPathNode = PathNode::fromNode($end);

        return amsterdam_distance($startPathNode->getX(), $startPathNode->getY(), $endPathNode->getX(), $endPathNode->getY());
    }

    /**
     * Create an index for the towers.
     *
     * @param Collection $towers
     * @return array
     */
    protected function indexTowers($towers)
    {
        return $towers->reduce(function($carry, $tower) {
            $carry[$tower->y][$tower->x] = $tower;

            return $carry;
        }, []);
    }

    /**
     * Create an index for the antagonists.
     *
     * @param Collection $antagonists
     * @return array
     */
    protected function indexAntagonists($antagonists)
    {
        return $antagonists->reduce(function($carry, $antagonist) {
            $carry[$antagonist->y][$antagonist->x] = $antagonist;

            return $carry;
        }, []);
    }

    /**
     * Whether two nodes are adjacent to eachother.
     *
     * @param PathNode $a
     * @param PathNode $b
     * @return boolean
     */
    protected function areAdjacent(PathNode $a, PathNode $b)
    {
        return abs($a->getY() - $b->getY()) <= 1 && abs($a->getX() - $b->getX()) <= 1;
    }
}
