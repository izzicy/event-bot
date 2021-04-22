<?php

namespace App\Mmg\Models;

use App\Mmg\Contracts\TileInterface;
use App\Models\DiscordUser;
use App\Services\Users\UserModelRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tile extends Model implements TileInterface
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    /** @return void */
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            $query->orderBy('y');
            $query->orderBy('x');
        });
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mmg_tiles';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /** @inheritDoc */
    public function getGame()
    {
        return $this->game;
    }

    /** @inheritDoc */
    public function getState()
    {
        return $this->state;
    }

    /** @inheritDoc */
    public function setState($state)
    {
        $this->state = $state;
    }

    /** @inheritDoc */
    public function getNearbyMineCount()
    {
        $game = $this->getGame();

        $mineCount = 0;

        foreach (range(-1, 1) as $x) {
            foreach (range(-1, 1) as $y) {
                $tile = $game->getTileAt((int)$this->x + $x, (int)$this->y + $y);

                if (
                    $tile != null
                    && ($x === 0 && $y === 0) === false
                    && $tile->getState() === 'mine'
                ) {
                    $mineCount += 1;
                }
            }
        }

        return $mineCount;
    }

    /** @inheritDoc */
    public function getConquerer()
    {
        return $this->conquerer;
    }

    /** @inheritDoc */
    public function setConquerer($user)
    {
        $this->conquerer()->associate(
            $this->getUserRepository()->retrieveFromInstance($user)
        );
    }

    /** @inheritDoc */
    public function getFlaggers()
    {
        return $this->flaggers;
    }

    /** @inheritDoc */
    public function addFlagger($user)
    {
        $this->flaggers()->syncWithoutDetaching(
            $this->getUserRepository()->retrieveFromInstance($user)
        );
    }

    /** @inheritDoc */
    public function removeFlagger($user)
    {
        $this->flaggers()->detach(
            $this->getUserRepository()->retrieveFromInstance($user)
        );
    }

    /**
     * Relationship with the game.
     *
     * @return BelongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class, 'mmg_id');
    }

    /**
     * The tile conquerer.
     *
     * @return BelongsTo
     */
    public function conquerer()
    {
        return $this->belongsTo(DiscordUser::class, 'conquerer_id');
    }

    /**
     * The tile flaggers.
     *
     * @return BelongsToMany
     */
    public function flaggers()
    {
        return $this->belongsToMany(DiscordUser::class, 'mmg_tile_flagger', 'tile_id', 'user_id');
    }

    /**
     * Get the user repository.
     *
     * @return UserModelRepository
     */
    protected function getUserRepository()
    {
        return app(UserModelRepository::class);
    }
}
