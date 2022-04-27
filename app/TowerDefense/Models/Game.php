<?php

namespace App\TowerDefense\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $channel_id
 * @property string $state
 * @property int $base_health
 * @property int $base_x
 * @property int $base_y
 * @property int $width
 * @property int $height
 * @property-read Collection $antagonists
 * @property-read Collection $players
 * @property-read Collection $towers
 */
class Game extends Model
{
    const STATE_WON = 'WON';
    const STATE_LOST = 'LOST';
    const STATE_PLAYING = 'PLAYING';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tower_defense_game';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relationship with the antagonists.
     *
     * @return HasMany
     */
    public function antagonists()
    {
        return $this->hasMany(Antagonist::class, 'tdg_id');
    }

    /**
     * Relationship with the players.
     *
     * @return HasMany
     */
    public function players()
    {
        return $this->hasMany(Player::class, 'tdg_id');
    }

    /**
     * Relationship with the towers.
     *
     * @return HasMany
     */
    public function towers()
    {
        return $this->hasMany(Tower::class, 'tdg_id');
    }
}
