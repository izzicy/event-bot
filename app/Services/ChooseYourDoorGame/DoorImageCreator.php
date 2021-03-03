<?php

namespace App\Services\ChooseYourDoorGame;

use Intervention\Image\ImageManagerStatic as Image;

class DoorImageCreator
{
    /**
     * Create a door image.
     * Returns the image path.
     *
     * @param int $numberOfDoors
     * @return string
     */
    public function create($numberOfDoors)
    {
        $background = Image::make(storage_path('app/choose-your-door/background.png'));
        $doorImage = Image::make(storage_path('app/choose-your-door/door.png'));
        $offsetY = 74;

        $backgroundHeight = $background->getHeight();
        $backgroundWidth = $background->getWidth();

        $ratio = $doorImage->getWidth() / $doorImage->getHeight();

        $doorHeight = round($backgroundHeight * 0.5);
        $doorWidth = round($ratio * $doorHeight);
        $doorImage->resize($doorWidth, $doorHeight);

        for ($i = 0; $i < $numberOfDoors; $i += 1) {
            $background->insert(
                $doorImage,
                'top-left',
                round($backgroundWidth / ($numberOfDoors + 1) * ($i + 1) - ($doorWidth / 2)),
                round($backgroundHeight - $doorHeight - $offsetY)
            );
        }

        $path = tempnam(sys_get_temp_dir(), 'image') . '.png';

        $background->save($path);

        return $path;
    }
}
