<?php

namespace App\Models;

use App\Services\StateGrid\StateGridInterface;
use Illuminate\Database\Eloquent\Model;

class StateGrid extends Model implements StateGridInterface
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'elements' => 'array',
    ];

    /**
     * Set the grid dimensions.
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function setDimensions($width, $height)
    {
        $this->elements = array_fill(0, $height, array_fill(0, $width, null));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setStateAt($x, $y, $state)
    {
        $elements = $this->elements;
        $elements[$y][$x] = $state;
        $this->elements = $elements;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStateAt($x, $y)
    {
        return $this->elements[$y][$x] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getWidth()
    {
        $elements = $this->elements;

        return count(reset($elements));
    }

    /**
     * @inheritdoc
     */
    public function getHeight()
    {
        return count($this->elements);
    }
}
