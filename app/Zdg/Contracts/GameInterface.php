<?php

namespace App\Zdg\Contracts;

use Illuminate\Support\LazyCollection;

interface GameInterface
{
    /**
     * Get the game width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get the game height.
     *
     * @return int
     */
    public function getHeight();

    /**
     * Get all changed pixels in order.
     *
     * @return LazyCollection
     */
    public function getChangedPixels();

    /**
     * Find all pixels with the given indexes.
     *
     * @param string[]|int[] $indexes
     * @return LazyCollection
     */
    public function findPixels($indexes);

    /**
     * Create a pixel at the given index.
     *
     * @param int $index
     * @return PixelInterface
     */
    public function createPixel($index);
}
