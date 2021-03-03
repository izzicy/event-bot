<?php

namespace App\Models\MultiplayerMinesweeper;

use App\Models\StateGrid;
use App\Services\MMGame\Contracts\PickableRepositoryInterface;
use App\Services\Users\UserInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MinesweeperGame extends Model implements PickableRepositoryInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'multiplay_minesweeper_games';

    /**
     * Create a new game.
     *
     * @param int $width
     * @param int $height
     * @return self
     */
    public static function createNewGame($width, $height)
    {
        $grid = new StateGrid();

        $grid->setDimensions($width, $height);

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                $grid->setStateAt($x, $y, 'empty');
            }
        }

        $grid->save();

        $game = self::forceCreate([
            'grid_id' => $grid->getKey(),
        ]);

        return $game;
    }

    /**
     * @inheritdoc
     */
    public function isPickable($tileX, $tileY, ?UserInterface $user)
    {
        return $this->conquered->filter(function($conquered) use ($tileX, $tileY) {
            return $conquered->x_coord === $tileX || $conquered->y_coord === $tileY;
        })->count() > 0;
    }

    /**
     * Relationship with the grid.
     *
     * @return BelongsTo
     */
    public function grid()
    {
        return $this->belongsTo(StateGrid::class, 'grid_id');
    }

    /**
     * Relationship with the conquered tiles.
     *
     * @return HasMany
     */
    public function conquered()
    {
        return $this->hasMany(ConqueredTile::class, 'game_id');
    }
}
