<?php

namespace App\Services\Sprites;

use App\Contracts\Sprites\SpriteRepository;

class AggregateSpriteRepository implements SpriteRepository
{
    /**
     * The sprite repositories.
     *
     * @var SpriteRepository[]
     */
    protected $spriteRepositories;

    /**
     * Construct a new aggregate sprite repository.
     *
     * @param SpriteRepository[] $spriteRepositories
     */
    public function __construct($spriteRepositories)
    {
        $this->spriteRepositories = $spriteRepositories;
    }

    /**
     * @inheritdoc
     */
    public function get($name, $direction)
    {
        foreach ($this->spriteRepositories as $repository) {
            if ($sprite = $repository->get($name, $direction)) {
                return $sprite;
            }
        }

        return null;
    }
}
