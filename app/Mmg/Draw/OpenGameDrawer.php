<?php

namespace App\Mmg\Draw;

class OpenGameDrawer extends AbstractGameDrawer
{
    /** @inheritdoc */
    public function draw($game)
    {
        $width = $game->getWidth();
        $height = $game->getHeight();

        $this->game = $game;
        $this->canvas = $this->createCanvas($width, $height);

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                $tile = $game->getTileAt($x, $y);
                $state = $tile->getState();

                if ($state === 'mine') {
                    $this->drawMineAt($x, $y);
                }
            }
        }

        return $this->canvas->getCore();
    }
}
