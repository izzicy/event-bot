<?php

namespace App\Services\Sprites;

use App\Contracts\Sprites\SpriteBuilder as SpriteBuilderContract;
use Illuminate\Contracts\Container\Container;
use Intervention\Image\Image;

class SpriteBuilder implements SpriteBuilderContract
{
    /**
     * The container implementation.
     *
     * @var Container
     */
    protected $con;

    /**
     * The original sprite image.
     *
     * @var Image
     */
    protected $image;

    /**
     * The stored sprites.
     *
     * @var array
     */
    protected $stored = [];

    /**
     * The sprite name.
     *
     * @var string
     */
    protected $name;

    /**
     * The X coordinate of the focus.
     *
     * @var integer
     */
    protected $focusX = 0;

    /**
     * The Y coordinate of the focus.
     *
     * @var integer
     */
    protected $focusY = 0;

    /**
     * The width of the focus.
     *
     * @var integer
     */
    protected $focusWidth = 1;

    /**
     * The height of the focus.
     *
     * @var integer
     */
    protected $focusHeight = 1;

    /**
     * The sprite width.
     *
     * @var integer
     */
    protected $spriteWidth = 1;

    /**
     * The sprite height.
     *
     * @var integer
     */
    protected $spriteHeight = 1;

    /**
     * The current direction this sprite is facing.
     *
     * @var string
     */
    protected $direction = COMPASS_EAST;

    /**
     * Consturct a new sprite builder.
     *
     * @param Container $con
     * @param Image $image
     */
    public function __construct(Container $con, Image $image)
    {
        $this->con = $con;
        $this->image = $image;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->con->make(SpriteRepository::class, [
            'sprites' => $this->stored,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function storeAllDirections()
    {
        $currAngle = compass_to_radians($this->direction);

        $offsetAngels = [
            0,
            M_PI_2,
            M_PI,
            M_PI + M_PI_2,
        ];

        foreach ($offsetAngels as $offsetAngle) {
            $image = (clone $this->image)
                ->crop(
                    $this->spriteWidth * $this->focusWidth,
                    $this->spriteHeight * $this->focusHeight,
                    $this->focusX * $this->spriteWidth,
                    $this->focusY * $this->spriteHeight
                )
                ->rotate($offsetAngle / M_PI * 180)
                ->encode('png');

            $this->stored[$this->name][align_to_compass($offsetAngle + $currAngle)] = $image;
        }
    }

    /**
     * @inheritdoc
     */
    public function storeSprite($direction)
    {
        $currAngle = compass_to_radians($this->direction);
        $destAngle = compass_to_radians($direction);
        $deltaAngle = $destAngle - $currAngle;

        $image = (clone $this->image)
            ->crop(
                $this->spriteWidth * $this->focusWidth,
                $this->spriteHeight * $this->focusHeight,
                $this->focusX * $this->spriteWidth,
                $this->focusY * $this->spriteHeight
            )
            ->rotate($deltaAngle / M_PI * 180)
            ->encode('png');

        $this->stored[$this->name][$direction] = $image;
    }

    /**
     * @inheritdoc
     */
    public function focusAt($x, $y, $width = 1, $height = 1)
    {
        $this->focusX = $x;
        $this->focusY = $y;
        $this->focusWidth = $width;
        $this->focusHeight = $height;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function spriteWidth($width = 1)
    {
        $this->spriteWidth = $width;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function spriteHeight($height = 1)
    {
        $this->spriteHeight = $height;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isFacingTo($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function clone()
    {
        return clone $this;
    }
}
