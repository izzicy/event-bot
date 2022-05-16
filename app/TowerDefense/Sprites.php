<?php

namespace App\TowerDefense;

use App\Contracts\Sprites\SpriteBuilderFactory;
use App\Contracts\Sprites\SpriteRepository;

class Sprites implements SpriteRepository
{
    const WALKING_1 = 'WALKING_1';
    const WALKING_2 = 'WALKING_2';

    const ATTACK_1 = 'ATTACK_1';
    const ATTACK_2 = 'ATTACK_2';

    const WALKING_WOUNDED_1 = 'WALKING_WOUNDED_1';
    const WALKING_WOUNDED_2 = 'WALKING_WOUNDED_2';

    const ATTACK_WOUNDED_1 = 'ATTACK_WOUNDED_1';
    const ATTACK_WOUNDED_2 = 'ATTACK_WOUNDED_2';

    /**
     * The sprites factory.
     *
     * @var SpriteBuilderFactory
     */
    protected $spritesFactory;

    /**
     * The sprites repository.
     *
     * @var SpriteRepository
     */
    protected $spritesRepository;

    public function __construct(SpriteBuilderFactory $spritesFactory)
    {
        $this->spritesFactory = $spritesFactory;

        $this->spritesRepository = $this->createAntagonistSpritesRepository();
    }

    /**
     * @inheritdoc
     */
    public function get($name, $direction)
    {
        return $this->spritesRepository->get($name, $direction);
    }

    /**
     * Create a sprites repository.
     *
     * @return SpriteRepository
     */
    protected function createAntagonistSpritesRepository()
    {
        $builder = $this->spritesFactory->createSpriteBuilder(
            config('tower-defense.asset_antagonist_sprites')
        );

        $builder->spriteWidth(32)->spriteHeight(32);

        // Walking
        $builder
            ->focusAt(0, 0)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::WALKING_1)
            ->storeAllDirections();

        $builder
            ->focusAt(1, 0)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::WALKING_2)
            ->storeAllDirections();

        $builder
            ->focusAt(2, 0)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::WALKING_1)
            ->storeAllDirections();

        $builder
            ->focusAt(3, 0)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::WALKING_2)
            ->storeAllDirections();

        // Attacking
        $builder
            ->focusAt(0, 1)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::ATTACK_1)
            ->storeAllDirections();

        $builder
            ->focusAt(1, 1)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::ATTACK_2)
            ->storeAllDirections();

        $builder
            ->focusAt(2, 1)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::ATTACK_1)
            ->storeAllDirections();

        $builder
            ->focusAt(3, 1)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::ATTACK_2)
            ->storeAllDirections();

        // Walking wounded
        $builder
            ->focusAt(0, 2)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::WALKING_WOUNDED_1)
            ->storeAllDirections();

        $builder
            ->focusAt(1, 2)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::WALKING_WOUNDED_2)
            ->storeAllDirections();

        $builder
            ->focusAt(2, 2)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::WALKING_WOUNDED_1)
            ->storeAllDirections();

        $builder
            ->focusAt(3, 2)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::WALKING_WOUNDED_2)
            ->storeAllDirections();

        // Attacking wounded
        $builder
            ->focusAt(0, 3)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::ATTACK_WOUNDED_1)
            ->storeAllDirections();

        $builder
            ->focusAt(1, 3)
            ->isFacingTo(COMPASS_SOUTH)
            ->name(self::ATTACK_WOUNDED_2)
            ->storeAllDirections();

        $builder
            ->focusAt(2, 3)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::ATTACK_WOUNDED_1)
            ->storeAllDirections();

        $builder
            ->focusAt(3, 3)
            ->isFacingTo(COMPASS_SOUTH_EAST)
            ->name(self::ATTACK_WOUNDED_2)
            ->storeAllDirections();

        return $builder->get();
    }
}
