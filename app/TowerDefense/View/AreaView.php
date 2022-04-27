<?php

namespace App\TowerDefense\View;

use Intervention\Image\ImageManagerStatic;

class AreaView
{
    const TILE_DIMENSIONS = 32;

    protected $antagonistUnhurtWalk

    public function __construct()
    {

    }

    public function draw(AreaData $areaData)
    {
        $width = $areaData->width();
        $height = $areaData->height();

        $img = ImageManagerStatic::canvas($width * self::TILE_DIMENSIONS, $height * self::TILE_DIMENSIONS, '#00f');


    }
}
