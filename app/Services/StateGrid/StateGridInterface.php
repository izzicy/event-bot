<?php

namespace App\Services\StateGrid;

interface StateGridInterface
{
    /**
     * Set the dimensions of the grid.
     * NOTE: Resets the states of the grid.
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function setDimensions($width, $height);

    /**
     * Get the state at the given x and y coordinates.
     *
     * @param int $x
     * @param int $y
     * @return string|null
     */
    public function getStateAt($x, $y);

    /**
     * Set the state at the given coordinates.
     *
     * @param int $x
     * @param int $y
     * @param string|null $state
     * @return $this
     */
    public function setStateAt($x, $y, $state);

    /**
     * Get the grid width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get the grid height.
     *
     * @return int
     */
    public function getHeight();
}
