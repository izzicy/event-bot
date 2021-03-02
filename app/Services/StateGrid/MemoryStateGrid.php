<?php

namespace App\Services\StateGrid;

use App\Services\StateGrid\StateGridInterface;

class MemoryStateGrid implements StateGridInterface
{
    /**
     * The grid elements.
     *
     * @var array
     */
    protected $elements = [];

    /**
     * @inheritdoc
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
        $this->elements[$y][$x] = $state;

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
        return count(reset($this->elements));
    }

    /**
     * @inheritdoc
     */
    public function getHeight()
    {
        return count($this->elements);
    }
}
