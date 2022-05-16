<?php

namespace App\Contracts\Sprites;

interface SpriteRepository
{
    /**
     * Get a sprite with the given name and direction.
     *
     * @param string $name
     * @param string $direction
     * @return $this
     */
    public function get($name, $direction);
}
