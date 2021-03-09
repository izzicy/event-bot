<?php

namespace App\Services\MMGame\Collections;

use App\Services\MMGame\Contracts\UserAssociatedTileInterface;
use App\Services\MMGame\Contracts\UserAssocTilesCollectionInterface;
use App\Services\Users\UserInterface;

class AssocTileCollection implements UserAssocTilesCollectionInterface
{
    /**
     * The associated tiles by coordinates.
     *
     * @var array[]UserAssociatedTileInterface[]
     */
    protected $assocTilesByCoords = [];

    /**
     * The associated tiles.
     *
     * @var UserAssociatedTileInterface[]
     */
    protected $assocTiles;

    /**
     * Collection constructor.
     *
     * @param UserAssociatedTileInterface[] $assocTiles
     */
    public function __construct($assocTiles)
    {
        foreach ($assocTiles as $tile) {
            $this->assocTilesByCoords[$tile->getX()][$tile->getY()] = $tile;
        }

        $this->assocTiles = $assocTiles;
    }

    /**
     * @inheritdoc
     */
    public function hasTileAt($x, $y): bool
    {
        return $this->getTileAt($x, $y) !== null;
    }

    /**
     * @inheritdoc
     */
    public function hasUserTileAt($x, $y, UserInterface $user): bool
    {
        $tile = $this->getTileAt($x, $y);

        if ($tile === null) {
            return false;
        }

        return $tile->getUser()->getId() == $user->getId();
    }

    /**
     * @inheritdoc
     */
    public function getTileAt($x, $y): ?UserAssociatedTileInterface
    {
        return $this->assocTilesByCoords[$x][$y] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->assocTiles;
    }
}
