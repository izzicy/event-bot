<?php

namespace App\Mmg\Models;

use App\Mmg\Contracts\GameInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model implements GameInterface
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'initialized' => 'boolean',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mmg';

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
        $this->intialize = true;
    }

    /** @inheritDoc */
    public function hasTileAt($x, $y)
    {
        return $this->tiles->get($this->getIndex($x, $y)) != null;
    }

    /** @inheritDoc */
    public function getTileAt($x, $y)
    {
        return $this->tiles->get($this->getIndex($x, $y));
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
        return $this->hasMany(Tile::class, 'game_id');
    }

    /** @inheritDoc */
    protected function getIndex($x, $y)
    {
        return $this->width * $y + $x;
    }
}
