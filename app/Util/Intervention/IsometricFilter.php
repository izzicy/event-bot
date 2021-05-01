<?php

namespace App\Util\Intervention;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class IsometricFilter implements FilterInterface
{
    const LEFT = 0;
    const RIGHT = 1;
    const LEFT_FLAT_BOTTOM_LEFT = 2;
    const LEFT_FLAT_BOTTOM_RIGHT = 3;
    const RIGHT_FLAT_BOTTOM_LEFT = 4;
    const RIGHT_FLAT_BOTTOM_RIGHT = 5;

    /** @var int */
    protected $orientation;

    /**
     * @param string $orientation
     */
    public function __construct($orientation = self::LEFT)
    {
        $this->orientation = $orientation;
    }

    /**
     * @inheritdoc
     */
    public function applyFilter(Image $image)
    {
        if ($this->orientation === self::LEFT) {
            // do nothing, the orientation is left by default
        } else if ($this->orientation === self::RIGHT) {
            $image->flip('h');
        } else if ($this->orientation === self::LEFT_FLAT_BOTTOM_LEFT) {
            // do nothing
        } else if ($this->orientation === self::LEFT_FLAT_BOTTOM_RIGHT) {
            // do nothing
        } else if ($this->orientation === self::RIGHT_FLAT_BOTTOM_LEFT) {
            $image->flip('h');
        } else if ($this->orientation === self::RIGHT_FLAT_BOTTOM_RIGHT) {
            $image->flip('h');
        }

        $height = $image->getHeight();
        $width = $image->getWidth();

        $stretchedWidth  = round($width * cos(30 / 180 * M_PI));
        $stretchedHeight = round($height + $width * sin(30 / 180 * M_PI));

        $stretchedImage = ImageManagerStatic::canvas($stretchedWidth, $stretchedHeight, [0, 0, 0, 0]);

        foreach (range(0, $stretchedWidth - 1) as $x) {
            foreach (range(0, $stretchedHeight) as $y) {
                $originalX = round($x - (($stretchedWidth - $width) * ($x / ($stretchedWidth - 1))));
                $originalY = round($y - (($stretchedHeight - $height) * ($x / ($stretchedWidth - 1))));

                if (
                    $originalY >= 0
                    && $originalY < $height
                    && $originalX >= 0
                    && $originalX < $width
                ) {
                    $colour = $image->pickColor($originalX, $originalY);

                    $stretchedImage->pixel($colour, $x, $y);
                }
            }
        }

        // Correct the mutations set for the orientation.
        if ($this->orientation === self::LEFT) {
            // do nothing, the orientation is left by default
        } else if ($this->orientation === self::RIGHT) {
            $stretchedImage->flip('h');
        } else if ($this->orientation === self::LEFT_FLAT_BOTTOM_LEFT) {
            $stretchedImage->rotate(60);
            $stretchedImage = $stretchedImage->trim('transparent', array('top', 'bottom'), 1);
        } else if ($this->orientation === self::LEFT_FLAT_BOTTOM_RIGHT) {
            $stretchedImage->rotate(-120);
            $stretchedImage = $stretchedImage->trim('transparent', array('top', 'bottom'), 1);
        } else if ($this->orientation === self::RIGHT_FLAT_BOTTOM_LEFT) {
            $stretchedImage->flip('h');
            $stretchedImage->rotate(-60);
            $stretchedImage = $stretchedImage->trim('transparent', array('top', 'bottom'), 1);
        } else if ($this->orientation === self::RIGHT_FLAT_BOTTOM_RIGHT) {
            $stretchedImage->flip('h');
            $stretchedImage->rotate(120);
            $stretchedImage = $stretchedImage->trim('transparent', array('top', 'bottom'), 1);
        }

        return $stretchedImage;
    }
}
