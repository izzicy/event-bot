<?php

namespace App\Zdg\Models;

use App\Zdg\Contracts\FactoryInterface;
use App\Zdg\Contracts\GameInterface;
use App\Zdg\Pixels\PixelsIndexesIterator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model implements GameInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zdg';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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
    public function getChangedPixels()
    {
        $this->pixels->loadMissing('game');

        return $this->pixels;
    }

    /** @inheritDoc */
    public function findPixels($indexes)
    {
        $cursor = $this->pixels()->whereIn('index', $indexes)->with('game')->cursor();

        return new PixelsIndexesIterator(app(FactoryInterface::class), $this, $indexes, $cursor);
    }

    /** @inheritDoc */
    public function createPixel($index)
    {
        return Pixel::create([
            'zdg_id' => $this->getKey(),
            'index' => $index,
            'r' => 255,
            'g' => 255,
            'b' => 255,
        ]);
    }

    /**
     * Relationship with pixels.
     *
     * @return HasMany
     */
    public function pixels()
    {
        return $this->hasMany(Pixel::class, 'zdg_id');
    }
}
