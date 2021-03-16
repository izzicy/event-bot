<?php

namespace App\Services\MMGame;

use App\Services\MMGame\Contracts\UserAssocTilesCollectionInterface;
use App\Services\StateGrid\StateGridInterface;

class MineDistributer
{
    /**
     * Distribute the mines over the grid.
     *
     * @param StateGridInterface $grid
     * @param UserAssocTilesCollectionInterface $picks
     * @param int $mineCount
     * @return $this
     */
    public function distribute(StateGridInterface $grid, UserAssocTilesCollectionInterface $picks, $mineCount)
    {
        $height = $grid->getHeight();
        $width = $grid->getWidth();

        $gridWithCounts = array_fill(0, $height, array_fill(0, $width, 0));

        foreach (range(0, $mineCount - 1) as $i) {
            $placed = false;

            do {
                $randX = rand(0, $width - 1);
                $randY = rand(0, $height - 1);

                if ($picks->hasTileAt($randX, $randY) === false && $gridWithCounts[$randX][$randY] !== 'mine') {
                    $gridWithCounts[$randX][$randY] = 'mine';

                    $this->incrementCountAt($gridWithCounts, $randX - 1, $randY);
                    $this->incrementCountAt($gridWithCounts, $randX, $randY - 1);
                    $this->incrementCountAt($gridWithCounts, $randX + 1, $randY);
                    $this->incrementCountAt($gridWithCounts, $randX, $randY + 1);

                    $this->incrementCountAt($gridWithCounts, $randX - 1, $randY - 1);
                    $this->incrementCountAt($gridWithCounts, $randX - 1, $randY + 1);
                    $this->incrementCountAt($gridWithCounts, $randX + 1, $randY - 1);
                    $this->incrementCountAt($gridWithCounts, $randX + 1, $randY + 1);

                    $placed = true;
                }
            } while ($placed === false);
        }

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                if ($gridWithCounts[$x][$y] === 'mine') {
                    $grid->setStateAt($x, $y, 'mine');
                } else if ((int) $gridWithCounts[$x][$y] === 0) {
                    $grid->setStateAt($x, $y, 'empty');
                } else {
                    $grid->setStateAt($x, $y, 'nearby_' . $gridWithCounts[$x][$y]);
                }
            }
        }

        return $this;
    }

    protected function incrementCountAt(&$gridWithCounts, $x, $y)
    {
        if (isset($gridWithCounts[$x][$y]) && $gridWithCounts[$x][$y] !== 'mine') {
            $gridWithCounts[$x][$y] += 1;
        }
    }
}
