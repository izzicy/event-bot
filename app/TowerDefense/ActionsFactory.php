<?php

namespace App\TowerDefense;

use App\TowerDefense\Actions\AntagonistTargetAction;
use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Game;
use App\TowerDefense\Models\Tower;
use Illuminate\Database\Eloquent\Collection;

class ActionsFactory
{
    public function __construct()
    {

    }

    public function advanceGame(Game $game)
    {

    }

    /**
     * Create an astart grid.
     *
     * @param Game $game
     * @return \BlackScorp\Astar\Grid
     */
    protected function createGrid(Game $game)
    {
        $map = [];

        $antagonistsDictionary = $game->antagonists->reduce(function($carry, $antagonist) {
            $carry[$antagonist->y][$antagonist->x] = $antagonist;

            return $carry;
        }, []);

        $towersDictionary = $game->towers->reduce(function($carry, $tower) {
            $carry[$tower->y][$tower->x] = $tower;

            return $carry;
        }, []);

        for ($y = 0; $y < $game->height; $y += 1) {
            $row = [];

            for ($x = 0; $x < $game->width; $x += 1) {
                $row[] = (int) (
                    isset($towersDictionary[$y][$x])
                    || isset($antagonistsDictionary[$y][$x])
                );
            }

            $map[] = $row;
        }

        return new \BlackScorp\Astar\Grid($map);
    }

    /**
     * Get the antagonists within the base range.
     *
     * @param Game $game
     * @return Collection
     */
    protected function getAntagonistsWithinBaseAttackDistance(Game $game)
    {
        $range = config('tower-defense.antagonist_base_range');

        return $game->antagonists->filter(function($antagonist) use ($game, $range) {
            return $this->distanceToBase($game, $antagonist) <= $range;
        });
    }

}
