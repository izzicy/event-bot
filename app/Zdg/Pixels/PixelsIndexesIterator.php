<?php

namespace App\Zdg\Pixels;

use App\Zdg\Contracts\FactoryInterface;
use App\Zdg\Contracts\GameInterface;
use App\Zdg\Contracts\PixelInterface;
use Illuminate\Support\LazyCollection;
use Iterator;

class PixelsIndexesIterator implements Iterator
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var GameInterface */
    protected $game;

    /** @var int */
    protected $currentIndex;

    /** @var int */
    protected $currentArrayIndex;

    /**
     * The indexes.
     *
     * @var int[]
     */
    protected $indexes;

    /**
     * The database iterator.
     *
     * @var Iterator
     */
    protected $iterator;

    /**
     * The current pixel retrieved from the collection.
     *
     * @var PixelInterface|null
     */
    protected $currentPixel;

    /**
     * Construct a new pixels indexes iterator.
     *
     * @param FactoryInterface $factory
     * @param GameInterface
     * @param int[] $indexes
     * @param LazyCollection $collection
     */
    public function __construct($factory, $game, $indexes, $collection)
    {
        $this->factory = $factory;
        $this->game = $game;
        $this->indexes = collect($indexes)->sort()->values()->all();
        $this->iterator = $collection->getIterator();

        $this->rewind();
    }

    /** @inheritDoc */
    public function current()
    {
        if (
            $this->currentPixel
            && $this->currentIndex == $this->currentPixel->getIndex()
        ) {
            return $this->currentPixel;
        }

        return $this->factory->createPlaceholderPixel($this->game, $this->currentIndex);
    }

    /** @inheritDoc */
    public function key()
    {
        return $this->currentIndex;
    }

    public function next()
    {
        $this->currentArrayIndex += 1;
        $this->currentIndex = $this->indexes[$this->currentArrayIndex] ?? null;

        if (
            $this->currentPixel
            && $this->currentIndex > $this->currentPixel->getIndex()
        ) {
            $this->iterator->next();

            if ($this->iterator->valid()) {
                $this->currentPixel = $this->iterator->current();
            } else {
                $this->currentPixel = null;
            }
        }
    }

    /** @inheritDoc */
    public function rewind()
    {
        $this->iterator->rewind();
        $this->currentPixel = $this->iterator->current();
        $this->currentIndex = reset($this->indexes);
        $this->currentArrayIndex = 0;
    }

    /** @inheritDoc */
    public function valid()
    {
        return count($this->indexes) > 0 && $this->currentArrayIndex < count($this->indexes);
    }
}
