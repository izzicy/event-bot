<?php

namespace App\Util\Intervention;

use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class ImageUtil
{
    /**
     * Get the dominating color.
     *
     * @param Image $image
     * @return array
     */
    public static function getDominatingColor(Image $image)
    {
        $background = ImageManagerStatic::canvas($image->getWidth(), $image->getHeight(), '#ffffff');
        $color = $background->insert($image)->limitColors(1)->pickColor(0, 0);

        $background->destroy();

        return $color;
    }
}
