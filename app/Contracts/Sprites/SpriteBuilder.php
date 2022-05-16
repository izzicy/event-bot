<?php

namespace App\Contracts\Sprites;

interface SpriteBuilder
{
    /**
     * Name the focused sprite.
     *
     * @param string $name
     * @return $this
     */
    public function name($name);

    /**
     * Get the sprite repository based on the stored sprites.
     *
     * @return SpriteRepository
     */
    public function get();

    /**
     * Store the sprite in its current form with the current direction.
     *
     * @param string $direction
     * @return $this
     */
    public function storeSprite($direction);

    /**
     * Store the file in all four directions.
     *
     * @return $this
     */
    public function storeAllDirections();

    /**
     * Focus at the given position.
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @return $this
     */
    public function focusAt($x, $y, $width = 1, $height = 1);

    /**
     * Set the sprite width of the sprites.
     *
     * @param integer $width
     * @return $this
     */
    public function spriteWidth($width = 1);

    /**
     * Set the sprite height of the sprites.
     *
     * @param integer $height
     * @return $this
     */
    public function spriteHeight($height = 1);

    /**
     * Pass along the direction this sprite is currently facing.
     *
     * @param string $direction
     * @return $this
     */
    public function isFacingTo($direction);

    /**
     * Create a copy of this sprite builder instance.
     *
     * @return static
     */
    public function clone();
}
