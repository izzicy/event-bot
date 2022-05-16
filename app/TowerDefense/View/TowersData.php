<?php

namespace App\TowerDefense\View;

use App\TowerDefense\Models\Tower;

class TowerData
{
    /**
     * The tower instance.
     *
     * @var Tower
     */
    protected $tower;

    /**
     * Create a new tower data instance.
     *
     * @param Tower $tower
     * @param array $data
     */
    public function __construct(Tower $tower, $data)
    {
        $this->tower = $tower;
        $this->data = $data;
    }

    /**
     * Whether this tower is attacking.
     *
     * @return boolean
     */
    public function isAttacking()
    {
        return $this->data['isAttacking'] ?? false;
    }

    /**
     * To what this tower is facing.
     *
     * @return string|null
     */
    public function facing()
    {
        return $this->data['facing'] ?? null;
    }

    /**
     * Whether this tower is dying.
     *
     * @return boolean
     */
    public function isDying()
    {
        return $this->tower->health <= 0;
    }

    /**
     * Whether this tower is recently build.
     *
     * @return boolean
     */
    public function isRecentlyBuild()
    {
        return $this->data['isRecentlyBuild'] ?? false;
    }

    /**
     * The x coordinate of the tower.
     *
     * @return int
     */
    public function getX()
    {
        return $this->tower->x;
    }

    /**
     * The y coordinate of the tower.
     *
     * @return int
     */
    public function getY()
    {
        return $this->tower->y;
    }
}
