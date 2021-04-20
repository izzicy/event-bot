<?php

namespace App\Mmg\Draw;

class OpenGameDrawer extends AbstractGameDrawer
{
    /** @inheritdoc */
    public function draw($game)
    {
        /** @var StateGridInterface */
        $grid = $game->grid;

        $width = $grid->getWidth();
        $height = $grid->getHeight();

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                $stateAtCoords = $grid->getStateAt($x, $y);

                if ($stateAtCoords === 'mine') {
                    $this->drawMineAt($x, $y);
                }
            }
        }

        return $this->canvas->getCore();
    }
}
