<?php

namespace App\Mmg\Models;

use App\Mmg\Contracts\GameInterface;
use App\Mmg\Contracts\TileInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model implements GameInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mmg';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'initialized' => 'boolean',
    ];

    /** @inheritDoc */
    public function getWidth()
    {
        return $this->width;
    }

    /** @inheritDoc */
    public function getHeight()
    {
        return $this->height;
    }

    /** @inheritDoc */
    public function hasInitialized()
    {
        return $this->initialized;
    }

    /** @inheritDoc */
    public function initialize()
    {
        $this->initialized = true;
    }

    /** @inheritDoc */
    public function hasTileAt($x, $y)
    {
        return $this->getTileAt($x, $y) != null;
    }

    /** @inheritDoc */
    public function getTileAt($x, $y)
    {
        if ($x < 0 || $x >= $this->width) {
            return null;
        }

        if ($y < 0 || $y >= $this->height) {
            return null;
        }

        return $this->tiles[$this->getIndex($x, $y)];
    }

    /** @inheritDoc */
    public function getTiles()
    {
        return $this->tiles;
    }

    /**
     * Relationship with tiles.
     *
     * @return HasMany
     */
    public function tiles()
    {
        return $this->hasMany(Tile::class, 'mmg_id');
    }

    /** @inheritDoc */
    protected function getIndex($x, $y)
    {
        return $this->width * $y + $x;
    }
}
