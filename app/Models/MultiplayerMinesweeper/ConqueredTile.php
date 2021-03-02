<?php

namespace App\Models\MultiplayerMinesweeper;

use App\Models\DiscordUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConqueredTile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'multiplayer_minesweeper_conquests';

    /**
     * Scope to only include tiles with the given user.
     *
     * @param Builder $query
     * @param mixed $user
     * @return Builder
     */
    public function scopeWhereUser($query, $user)
    {
        if (method_exists($user, 'getKey')) {
            $user = $user->getKey();
        }

        if (method_exists($user, 'getId')) {
            $user = $user->getId();
        }

        return $query->where('user_id', $user);
    }

    /**
     * Relationship with the game.
     *
     * @return BelongsTo
     */
    public function game()
    {
        return $this->belongsTo(MinesweeperGame::class, 'game_id');
    }

    /**
     * Relationship with users.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(DiscordUser::class, 'user_id');
    }
}
