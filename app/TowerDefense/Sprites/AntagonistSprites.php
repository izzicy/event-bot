<?php

namespace App\TowerDefense\Sprites;

use App\Services\Sprites\AbstractSpriteRepository;
use Intervention\Image\ImageManagerStatic;

class AntagonistSprites extends AbstractSpriteRepository
{
    const WALKING_1 = 'WALKING_1';
    const WALKING_2 = 'WALKING_2';

    /**
     * Construct a new repository.
     */
    public function __construct()
    {
        $sprites = ImageManagerStatic::make(
            config('tower-defense.asset_antagonist_sprites')
        );

        $this->storeSprite($sprites, self::WALKING_1, COMPASS_SOUTH, 0, 0, 32, 32, 0);
        $this->storeSprite($sprites, self::WALKING_1, COMPASS_SOUTH_WEST, 64, 0, 32, 32, 90);
        $this->storeSprite($sprites, self::WALKING_1, COMPASS_WEST, 0, 0, 32, 32, 90);
        $this->storeSprite($sprites, self::WALKING_1, COMPASS_NORTH_WEST, 64, 0, 32, 32, 180);
        $this->storeSprite($sprites, self::WALKING_1, COMPASS_NORTH, 0, 0, 32, 32, 180);
        $this->storeSprite($sprites, self::WALKING_1, COMPASS_NORTH_EAST, 64, 0, 32, 32, 270);
        $this->storeSprite($sprites, self::WALKING_1, COMPASS_EAST, 0, 0, 32, 32, 270);
        $this->storeSprite($sprites, self::WALKING_1, COMPASS_SOUTH_EAST, 64, 0, 32, 32, 0);
    }
}
