<?php

namespace App\Services\Sprites;

use App\Contracts\Sprites\SpriteRepository as SpriteRepositoryContract;

class SpriteRepository implements SpriteRepositoryContract
{
    /**
     * The generated sprites.
     *
     * @var array
     */
    protected $sprites;

    /**
     * Construct a new sprite repository.
     *
     * @param array $sprites
     */
    public function __construct($sprites)
    {
        $this->sprites = $sprites;
    }

    /**
     * Get the given sprite.
     *
     * @param string $name
     * @param string $direction
     * @return \Intervention\Image\Image|null
     */
    public function get($name, $direction)
    {
        if (isset($this->sprites[$name][$direction])) {
            return clone $this->sprites[$name][$direction];
        }

        return null;
    }
}
