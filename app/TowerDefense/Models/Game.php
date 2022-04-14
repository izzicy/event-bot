<?php

namespace App\TowerDefense;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tower_defense_game';

    /**
     * Relationship with the antagonists.
     *
     * @return HasMany
     */
    public function antagonists()
    {
        return $this->hasMany(Antagonist::class);
    }

    /**
     * Relationship with the players.
     *
     * @return HasMany
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Relationship with the towers.
     *
     * @return HasMany
     */
    public function towers()
    {
        return $this->hasMany(Tower::class);
    }
}
