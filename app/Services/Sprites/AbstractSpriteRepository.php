<?php

namespace App\Services\Sprites;

abstract class AbstractSpriteRepository
{
    /**
     * The generated sprites.
     *
     * @var array
     */
    protected $sprites;

    /**
     * Get the given sprite.
     *
     * @param string $name
     * @param string $direction
     * @return \Intervention\Image\Image|null
     */
    public function getSprite($name, $direction)
    {
        return $this->sprites[$name][$direction] ?? null;
    }

    /**
     * Store a new sprite created from the image.
     *
     * @param \Intervention\Image\Image $image
     * @param string $name
     * @param string $direction
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param int $rotation
     * @return void
     */
    protected function storeSprite($image, $name, $direction, $x, $y, $width, $height, $rotation)
    {
        $sprite = (clone $image)->crop($width, $height, $x, $y)->rotate($rotation)->encode('png');

        $this->sprites[$name][$direction] = $sprite;
    }
}
