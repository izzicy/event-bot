<?php

namespace App\Zdg\Pixels;

use App\Zdg\Contracts\GameInterface;
use App\Zdg\Contracts\PixelInterface;

class PlaceholderPixel implements PixelInterface
{
    /** @var GameInterface */
    protected $game;

    /** @var PixelInterface|null */
    protected $realPixel = null;

    /** @var int */
    protected $index;

    /**
     * Construct a new placeholder pixel interface.
     *
     * @param GameInterface $game
     * @param int $index
     */
    public function __construct(GameInterface $game, $index)
    {
        $this->game = $game;
        $this->index = $index;
    }

    /** @inheritDoc */
    public function getIndex()
    {
        return $this->index;
    }

    /** @inheritDoc */
    public function getX()
    {
        return $this->index % $this->game->getWidth();
    }

    /** @inheritDoc */
    public function getY()
    {
        return floor($this->index / $this->game->getWidth());
    }

    /** @inheritDoc */
    public function setPainter($user)
    {
        if (is_null($this->realPixel)) {
            $this->realPixel = $this->game->createPixel($this->index);
        }

        $this->realPixel->setPainter($user);
    }

    /** @inheritDoc */
    public function setRgb($red, $green, $blue)
    {
        if (is_null($this->realPixel)) {
            $this->realPixel = $this->game->createPixel($this->index);
        }

        $this->realPixel->setRgb($red, $green, $blue);
    }

    /** @inheritDoc */
    public function getRgb()
    {
        if (is_null($this->realPixel) === false) {
            return $this->realPixel->getRgb();
        }

        return [255, 255, 255];
    }
}
