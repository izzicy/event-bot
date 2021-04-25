<?php

namespace App\Zdg\Models;

use App\Services\Users\UserModelRepository;
use App\Zdg\Contracts\PixelInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pixel extends Model implements PixelInterface
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zdg_pixels';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /** @inheritDoc */
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            $query->orderBy('index', 'asc');
        });
    }

    /** @inheritDoc */
    public function getIndex()
    {
        return $this->index;
    }

    /** @inheritDoc */
    public function getX()
    {
        return ($this->index % $this->game->width);
    }

    /** @inheritDoc */
    public function getY()
    {
        return floor($this->index / $this->game->width);
    }

    /** @inheritDoc */
    public function setPainter($user)
    {
        app(UserModelRepository::class)->retrieveFromInstance($user);

        $this->user_id = $user->getId();
        $this->save();
    }

    /** @inheritDoc */
    public function setRgb($red, $green, $blue)
    {
        $this->r = $red;
        $this->g = $green;
        $this->b = $blue;

        $this->save();
    }

    /** @inheritDoc */
    public function getRgb()
    {
        return [$this->r, $this->g, $this->b];
    }

    /**
     * Relationship with the game.
     *
     * @return BelongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class, 'zdg_id');
    }
}
