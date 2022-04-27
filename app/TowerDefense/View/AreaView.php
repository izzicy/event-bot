<?php

namespace App\TowerDefense\View;

use App\TowerDefense\Sprites\AntagonistSprites;
use Intervention\Image\ImageManagerStatic;

class AreaView
{
    const TILE_DIMENSIONS = 32;

    /**
     * The antagonist sprites.
     *
     * @var AntagonistSprites
     */
    protected $antagonistSprites;

    public function __construct(AntagonistSprites $antagonistSprites)
    {
        $this->antagonistSprites = $antagonistSprites;
    }

    public function draw(AreaData $areaData)
    {
        $width = $areaData->width();
        $height = $areaData->height();

        $img = ImageManagerStatic::canvas($width * self::TILE_DIMENSIONS, $height * self::TILE_DIMENSIONS, '#826B3B')->encode('png');

        foreach ($areaData->antagonists() as $antogonist) {
            $facing = $antogonist->facing() ?? COMPASS_EAST;
            $x = $antogonist->getX();
            $y = $antogonist->getY();

            $sprite = $this->antagonistSprites->getSprite(AntagonistSprites::WALKING_1, $facing);

            $img->insert($sprite, 'top-left', $x * self::TILE_DIMENSIONS, $y * self::TILE_DIMENSIONS);
        }

        return $img;
    }
}
