<?php

namespace App\TowerDefense\View;

use App\TowerDefense\Models\Antagonist;

class AntagonistData
{
    /**
     * The antagonist instance.
     *
     * @var Antagonist
     */
    protected $antagonist;

    /**
     * Create a new antagonist data instance.
     *
     * @param Antagonist $antagonist
     * @param array $data
     */
    public function __construct(Antagonist $antagonist, $data)
    {
        $this->antagonist = $antagonist;
        $this->data = $data;
    }

    /**
     * Whether this antagonist is attacking.
     *
     * @return boolean
     */
    public function isAttacking()
    {
        return $this->data['isAttacking'] ?? false;
    }

    /**
     * To what this antagonist is facing.
     *
     * @return string|null
     */
    public function facing()
    {
        return $this->data['facing'] ?? null;
    }

    /**
     * Whether this antagonist is dying.
     *
     * @return boolean
     */
    public function isDying()
    {
        return $this->antagonist->health <= 0;
    }

    /**
     * Whether this antagonist is moving.
     *
     * @return boolean
     */
    public function isMoving()
    {
        return $this->data['isMoving'] ?? false;
    }

    /**
     * The x coordinate of the antagonist.
     *
     * @return int
     */
    public function getX()
    {
        return $this->antagonist->x;
    }

    /**
     * The y coordinate of the antagonist.
     *
     * @return int
     */
    public function getY()
    {
        return $this->antagonist->y;
    }
}
