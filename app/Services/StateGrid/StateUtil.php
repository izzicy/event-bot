<?php

namespace App\Services\StateGrid;

use App\Services\StateGrid\StateGridInterface;

class StateUtil
{
    /**
     * Returns the first position of the given state.
     * Null if no such state is found.
     *
     * @param StateGridInterface $grid
     * @param string $state
     * @return array|null
     */
    public static function findState(StateGridInterface $grid, $state)
    {
        $height = $grid->getHeight();
        $width = $grid->getWidth();

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($grid->getStateAt($x, $y) === $state) {
                    return [$x, $y];
                }
            }
        }
    }

    /**
     * Returns the all positions of the given state.
     *
     * @param StateGridInterface $grid
     * @param string $state
     * @return array
     */
    public static function findAllStates(StateGridInterface $grid, $state)
    {
        $height = $grid->getHeight();
        $width = $grid->getWidth();
        $positions = [];

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($grid->getStateAt($x, $y) === $state) {
                    $positions[] = [$x, $y];
                }
            }
        }

        return $positions;
    }
}
