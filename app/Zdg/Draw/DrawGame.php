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
     * @param bool $withGrid
     * @return resource
     */
    public function draw(GameInterface $game, $withGrid = false)
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

        if ($withGrid === false) {
            return $canvas->getCore();
        }

        foreach (range(1, $width - 1) as $x) {
            $canvas->rectangle($x * $pixelSize, 0, $x * $pixelSize, $height * $pixelSize, function ($draw) {
                $draw->background('rgba(0, 0, 0, 0)');
                $draw->border(1, 'rgba(0, 0, 0, 0.07)');
            });
        }

        foreach (range(1, $height - 1) as $y) {
            $canvas->rectangle(0, $y * $pixelSize, $width * $pixelSize, $y * $pixelSize, function ($draw) {
                $draw->background('rgba(0, 0, 0, 0)');
                $draw->border(1, 'rgba(0, 0, 0, 0.07)');
            });
        }

        $outerCanvas = ImageManagerStatic::canvas(($width + 2) * $pixelSize, ($height + 2) * $pixelSize, '#36393F');

        foreach (range(0, $width - 1) as $x) {
            $outerCanvas->text($x + 1, round($pixelSize * 1.5) + $x * $pixelSize, round($pixelSize / 2), function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });

            $outerCanvas->text($x + 1, round($pixelSize * 1.5) + $x * $pixelSize, round($pixelSize * 1.5) + $height * $pixelSize, function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });
        }

        foreach (range(0, $height - 1) as $y) {
            $outerCanvas->text($height - $y, round($pixelSize / 2), round($pixelSize * 1.5) + $y * $pixelSize, function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });

            $outerCanvas->text($height - $y, round($pixelSize * 1.5) + $width * $pixelSize, round($pixelSize * 1.5) + $y * $pixelSize, function($font) {
                $font->file(config('zdg.font-path'));
                $font->size(6);
                $font->color('#dddddd');
                $font->align('center');
                $font->valign('middle');
            });
        }

        $outerCanvas->insert($canvas, 'top-left', $pixelSize, $pixelSize);

        return $outerCanvas->getCore();
    }
}
