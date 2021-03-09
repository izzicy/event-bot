<?php

namespace App\Services\MMGame;

use App\Models\MultiplayerMinesweeper\MinesweeperGame;
use App\Services\StateGrid\StateGridInterface;
use Intervention\Image\ImageManagerStatic;

class GameDrawer
{
    /**
     * Draw the minesweeper game.
     *
     * @param MinesweeperGame $game
     * @return string
     */
    public function draw(MinesweeperGame $game)
    {
        $tile = ImageManagerStatic::make(config('mmg.tile-image-path'));

        $tileSize = config('mmg.tile-size');

        $tile->resize($tileSize, $tileSize);

        /** @var StateGridInterface */
        $grid = $game->grid;

        $width = $grid->getWidth();
        $height = $grid->getHeight();

        $canvas = ImageManagerStatic::canvas($width * $tileSize, $height * $tileSize, array(0, 0, 0, 0));

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                $conquered = $game->conquered->filter()

                $canvas->insert($tile, 'top-left', $x * $tileSize, $y * $tileSize);
            }
        }
    }
}
