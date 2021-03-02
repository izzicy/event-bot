<?php

namespace App\Models\MultiplayerMinesweeper;

use App\Models\StateGrid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MinesweeperGame extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'multiplay_minesweeper_games';

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
