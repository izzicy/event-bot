<?php

namespace App\Zdg\Contracts;

use App\Services\Users\UserInterface;

interface FactoryInterface
{
    /**
     * Create a placeholder pixel.
     *
     * @param GameInterface $game
     * @param int $index
     * @return PixelInterface
     */
    public function createPlaceholderPixel(GameInterface $game, $index): PixelInterface;
}
