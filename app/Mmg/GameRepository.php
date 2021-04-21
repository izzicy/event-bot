<?php

namespace App\Mmg;

use App\Mmg\Models\Game;
use App\Mmg\Models\Tile;
use Illuminate\Database\Eloquent\Collection;

class GameRepository
{
    /**
     * Create a new game.
     *
     * @param int $width
     * @param int $height
     * @return GameInterface
     */
    public function create($width, $height): Game
    {
        $game = Game::create([
            'width' => $width,
            'height' => $height,
        ]);

        $tiles = new Collection();

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                $tiles[] = Tile::create([
                    'mmg_id' => $game->getKey(),
                    'x'      => $x,
                    'y'      => $y,
                    'state'  => 'unknown',
                ]);
            }
        }

        return $game;
    }

    /**
     * Persist the game.
     *
     * @param Game $game
     * @return void
     */
    public function persist($game)
    {
        $game->save();
    }

    /**
     * Find a game.
     *
     * @param string $id
     * @return Game|null
     */
    public function find($id)
    {
        return Game::find($id);
    }
}
