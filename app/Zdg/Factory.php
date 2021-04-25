<?php

namespace App\Zdg;

use App\Zdg\Contracts\FactoryInterface;
use App\Zdg\Contracts\GameInterface;
use App\Zdg\Contracts\PixelInterface;
use App\Zdg\Pixels\PlaceholderPixel;

class Factory implements FactoryInterface
{
    /** @inheritDoc */
    public function createPlaceholderPixel(GameInterface $game, $index): PixelInterface
    {
        return new PlaceholderPixel($game, $index);
    }
}
