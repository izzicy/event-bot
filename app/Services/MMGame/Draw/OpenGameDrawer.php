<?php

namespace App\Services\MMGame\Draw;

class OpenGameDrawer extends AbstractGameDrawer
{
    /**
     * @inheritdoc
     */
    public function draw()
    {
        $game = $this->game;

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

        $path = tempnam(sys_get_temp_dir(), 'image') . '.png';

        $this->canvas->save($path);

        return $path;
    }
}
