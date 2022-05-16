<?php

namespace App\Services\Sprites;

use App\Contracts\Sprites\SpriteBuilderFactory as SpriteBuilderFactoryContract;
use Illuminate\Contracts\Container\Container;
use Intervention\Image\ImageManagerStatic;

class SpriteBuilderFactory implements SpriteBuilderFactoryContract
{
    /**
     * The container implemenation.
     *
     * @var Container
     */
    protected $con;

    /**
     * Construct a new sprite factory.
     *
     * @param Container $con
     */
    public function __construct(Container $con)
    {
        $this->con = $con;
    }

    /**
     * @inheritdoc
     */
    public function createSpriteBuilder($path)
    {
        $image = ImageManagerStatic::make($path);

        return $this->con->make(SpriteBuilder::class, [
            'image' => $image,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function createAggregateSpriteRepository($spriteRepositories)
    {
        return $this->con->make(AggregateSpriteRepository::class, [
            'spriteRepositories' => $spriteRepositories,
        ]);
    }
}
