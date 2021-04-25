<?php

namespace App\Zdg\Contracts;

use App\Services\Users\UserInterface;

interface PixelInterface
{
    /**
     * get the pixel index.
     *
     * @return int
     */
    public function getIndex();

    /**
     * Get the x coordinate.
     *
     * @return int
     */
    public function getX();

    /**
     * Get the y coordinate.
     *
     * @return int
     */
    public function getY();

    /**
     * Set the painting user.
     *
     * @param UserInterface $user
     * @return void
     */
    public function setPainter($user);

    /**
     * Set the pixel rgb.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return void
     */
    public function setRgb($red, $green, $blue);

    /**
     * Get the pixel rgb.
     *
     * @return int[]
     */
    public function getRgb();
}
