<?php

namespace App\Services\MMGame;

use App\Mmg\Contracts\GameOperatorInterface;

class MineDistributer implements GameOperatorInterface
{
    /** @var array[]int[] */
    protected $pickedTiles;

    /** @var int */
    protected $mineCount;

    /**
     * Mine distributer constructor.
     *
     * @param array[]int[] $pickedTiles
     * @param int $mineCount
     */
    public function __construct($pickedTiles, $mineCount)
    {
        $this->pickedTiles = $pickedTiles;
        $this->mineCount = $mineCount;
    }

    /** @inheritDoc */
    public function operateGame($game)
    {
        $pickDict = collect($this->pickedTiles)->mapWithKeys(function($pick) {
            return [
                ($pick[0] . '|' . $pick[1]) => true,
            ];
        });
        $mineCount = $this->mineCount;
        $height = $game->getHeight();
        $width = $game->getWidth();

        foreach (range(0, $mineCount - 1) as $i) {
            $placed = false;

            do {
                $randX = rand(0, $width - 1);
                $randY = rand(0, $height - 1);
                $tile = $game->getTileAt($randX, $randY);

                if ($pickDict->has($randX . '|' . $randY) === false && $tile->getState() !== 'mine') {
                    $tile->setState('mine');
                    $placed = true;
                }
            } while ($placed === false);
        }

        return $this;
    }
}
