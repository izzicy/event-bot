<?php

namespace App\TowerDefense\View;

use App\TowerDefense\Sprites;
use Illuminate\Support\Arr;
use Intervention\Image\ImageManagerStatic;

class AreaView
{
    const TILE_DIMENSIONS = 32;
    const GIF_DELAY = .6;

    /**
     * The sprites.
     *
     * @var Sprites
     */
    protected $sprites;

    public function __construct(Sprites $sprites)
    {
        $this->sprites = $sprites;
    }

    public function draw(AreaData $areaData)
    {
        $back1 = $this->createBackground($areaData);
        $back2 = $this->createBackground($areaData);

        $this->drawOnBackground($areaData, $back1, true);
        $this->drawOnBackground($areaData, $back2);

        return $this->createGif([
            $back1,
            $back2
        ]);
    }

    /**
     * Draw on the background.
     *
     * @param AreaData $areaData
     * @param \Intervention\Image\Image $background
     * @param boolean $firstFrame
     * @return void
     */
    protected function drawOnBackground(AreaData $areaData, $background, $firstFrame = false)
    {
        foreach ($areaData->antagonists() as $antagonist) {
            $facing = $antagonist->facing() ?? COMPASS_EAST;
            $x = $antagonist->getX();
            $y = $antagonist->getY();

            if ($antagonist->isAttacking()) {
                $sprite = $this->sprites->get($firstFrame ? Sprites::ATTACK_1 : Sprites::ATTACK_2, $facing);
            } else {
                $sprite = $this->sprites->get($firstFrame ? Sprites::WALKING_1 : Sprites::WALKING_2, $facing);
            }

            $background->insert($sprite, 'bottom-left', $x * self::TILE_DIMENSIONS, $y * self::TILE_DIMENSIONS);
        }
    }

    /**
     * Create the background image.
     *
     * @param AreaData $areaData
     * @return \Intervention\Image\Image
     */
    protected function createBackground(AreaData $areaData)
    {
        $width = $areaData->width();
        $height = $areaData->height();

        return ImageManagerStatic::canvas($width * self::TILE_DIMENSIONS, $height * self::TILE_DIMENSIONS, '#826B3B')->encode('gif');
    }

    /**
     * Create a gif image.
     *
     * @param \Intervention\Image\Image[] $frames
     * @return string
     */
    protected function createGif($frames)
    {
        $frames = Arr::wrap($frames);

        /** @var \Intervention\Image\Image */
        $img = Arr::first($frames);

        $gif = \Intervention\Gif\Builder::canvas($img->getWidth(), $img->getHeight(), 0);

        foreach ($frames as $frame) {
            $gif->addFrame((string) (clone $frame)->encode('gif'), self::GIF_DELAY);
        }

        $path = tempnam(sys_get_temp_dir(), '') . '.gif';

        file_put_contents($path, $gif->encode());

        return $path;
    }
}
