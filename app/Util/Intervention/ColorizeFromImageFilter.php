<?php

namespace App\Util\Intervention;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class ColorizeFromImageFilter extends FilterInterface
{
    /**
     * The image from which we colorize.
     *
     * @var Image
     */
    protected $fromImage;

    /**
     * The colorizing strength.
     *
     * @var int
     */
    protected $strength;

    /**
     * Construct a colorize filter.
     * Strength goes from 0 to 100.
     *
     * @param Image $fromImage
     * @param integer $strength
     */
    public function __construct(Image $fromImage, int $strength = 40)
    {
        $this->fromImage = $fromImage;
        $this->strength = $strength;
    }

    /**
     * @inheritdoc
     */
    public function applyFilter(Image $image)
    {
        list($r, $g, $b) = ImageUtil::getDominatingColor($image);

        $image->colorize(
            ($r - (255 / 2)) / (255 / 2) * $this->strength,
            ($g - (255 / 2)) / (255 / 2) * $this->strength,
            ($b - (255 / 2)) / (255 / 2) * $this->strength
        );

        return $image;
    }
}
