<?php

namespace App\Zdg\Draw;

use App\Zdg\Contracts\GameInterface;
use App\Zdg\Contracts\PixelInterface;
use Intervention\Image\ImageManagerStatic;

class DrawGame
{
    /**
     * Draw the zdg.
     *
     * @param GameInterface $game
     * @return resource
     */
    public function draw(GameInterface $game)
    {
        $width = $game->getWidth();
        $height = $game->getHeight();
        $pixelSize = config('zdg.pixel-size');
        $canvas = ImageManagerStatic::canvas($width * $pixelSize, $height * $pixelSize, '#ffffff');

        $game->getChangedPixels()->each(function(PixelInterface $pixel) use ($pixelSize, $canvas) {
            $x = $pixel->getX();
            $y = $pixel->getY();

            $canvas->insert(
                ImageManagerStatic::canvas($pixelSize, $pixelSize, $pixel->getRgb()),
                'top-left',
                $x * $pixelSize,
                $y * $pixelSize
            );
        });

        return $canvas->getCore();
    }
}
