<?php

namespace App\Services\MMGame\Collections;

use App\Services\MMGame\Contracts\UserAssociatedTileInterface;
use App\Services\MMGame\Contracts\UserAssocTilesCollectionInterface;
use App\Services\Users\UserInterface;

class AggregateAssocTileCollection implements UserAssocTilesCollectionInterface
{
    /**
     * The collections.
     *
     * @var UserAssocTilesCollectionInterface[]
     */
    protected $collections = [];

    /**
     * Create an aggrate collection.
     *
     * @param UserAssocTilesCollectionInterface[] $collections
     */
    public function __construct($collections)
    {
        $this->collections = $collections;
    }

    /**
     * @inheritdoc
     */
    public function hasTileAt($x, $y): bool
    {
        foreach ($this->collections as $collection) {
            if ($collection->hasTileAt($x, $y)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function hasUserTileAt($x, $y, UserInterface $user): bool
    {
        foreach ($this->collections as $collection) {
            if ($collection->hasUserTileAt($x, $y, $user)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTileAt($x, $y): ?UserAssociatedTileInterface
    {
        foreach ($this->collections as $collection) {
            if ($collection->hasTileAt($x, $y)) {
                return $collection->getTileAt($x, $y);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        $tiles = [];

        foreach ($this->collections as $collection) {
            $tiles = array_merge($tiles, $collection->all());
        }

        return $tiles;
    }
}
