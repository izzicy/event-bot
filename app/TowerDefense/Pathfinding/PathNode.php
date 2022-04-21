<?php

namespace App\TowerDefense\Pathfinding;

use JMGQ\AStar\AbstractNode;
use JMGQ\AStar\Node;

class PathNode extends AbstractNode
{
    /**
     * The x coordinate of the path node.
     *
     * @var int
     */
    protected $x;

    /**
     * The y coordinate of the path node.
     *
     * @var int
     */
    protected $y;

    /**
     * @param Node $node
     * @return PathNode
     */
    public static function fromNode(Node $node)
    {
        list($x, $y) = explode('x', $node->getID());

        return new PathNode($x, $y);
    }

    /**
     * @param int $x
     * @param int $y
     * @return PathNode
     */
    public static function fromCoords($x, $y)
    {
        return new PathNode($x, $y);
    }

    /**
     * Construct a new node with the two coordinates.
     *
     * @param int $x
     * @param int $y
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @inheritdoc
     */
    public function getID()
    {
        return $this->x . 'x' . $this->y;
    }

    /**
     * Get the x coordinate.
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Get the y coordinate.
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }
}
