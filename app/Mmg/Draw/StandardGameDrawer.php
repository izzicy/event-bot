<?php

namespace App\Mmg\Draw;

class StandardGameDrawer extends AbstractGameDrawer
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

                if ($state === 'empty') {
                    if ($conquerer = $tile->getConquerer()) {
                        $this->drawColoredSquareAt($x, $y, $conquerer);

                        if ($tile->getNearbyMineCount() > 0) {
                            $this->drawCountAt($x, $y, $tile->getNearbyMineCount());
                        }
                    } else {
                        $this->drawEmptyTileAt($x, $y);
                    }
                } else if ($state === 'mine') {
                    if ($conquerer = $tile->getConquerer()) {
                        $this->drawMineAt($x, $y);
                    } else {
                        $this->drawEmptyTileAt($x, $y);
                    }
                } else if ($state === 'unknown') {
                    $this->drawEmptyTileAt($x, $y);
                }

                $this->drawFlaggersAt($x, $y ,$tile->getFlaggers());
            }
        }

        return $this->canvas->getCore();
    }
}
