<?php

namespace App\Services\MMGame;

use App\Services\StateGrid\StateGridInterface;

class MineDistributer
{
    /**
     * Distribute the mines over the grid.
     *
     * @param StateGridInterface $grid
     * @param UserTilePicksCollection $picks
     * @param int $mineCount
     * @return $this
     */
    public function distribute(StateGridInterface $grid, UserTilePicksCollection $picks, $mineCount)
    {
        $height = $grid->getHeight();
        $width = $grid->getWidth();

        foreach (range(0, $mineCount - 1) as $i) {
            $placed = false;

            do {
                $randX = rand(0, $width - 1);
                $randY = rand(0, $height - 1);

                $placed = $picks->isPicked($randX, $randY);

                if ($placed) {
                    $grid->setStateAt($randX, $randY, 'mine');
                }
            } while ($placed === false);
        }

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                if ($grid->getStateAt($x, $y) !== 'mine') {
                    $grid->setStateAt($x, $y, 'empty');
                }
            }
        }

        return $this;
    }
}
