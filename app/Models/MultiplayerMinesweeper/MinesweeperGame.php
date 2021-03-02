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
