<?php

namespace App\Contracts\Sprites;

interface SpriteBuilderFactory
{
    /**
     * Create a sprite builder from the given filepath.
     *
     * @param string $path
     * @return SpriteBuilder
     */
    public function createSpriteBuilder($path);

    /**
     * Create an aggregate sprite repository.
     *
     * @param SpriteRepository[] $spriteRepositories
     * @return SpriteRepository
     */
    public function createAggregateSpriteRepository($spriteRepositories);
}
