<?php

namespace App\TowerDefense\Contracts;

use App\TowerDefense\Models\Antagonist;
use App\TowerDefense\Models\Tower;

interface EventsDirector
{
    /**
     * Notify that the antagnonist is attacking something.
     *
     * @param Antagonist $antagonist
     * @param Tower|null $tower
     * @param boolean $isAttackingBase
     * @return void
     */
    public function antagonistIsAttacking($antagonist, $tower = null, $isAttackingBase = false);

    /**
     * Notify that an antagnonist has just moved.
     *
     * @param Antagonist $antagonist
     * @param int $prevX
     * @param int $prevY
     * @return void
     */
    public function antagonistHasMoved($antagonist, $prevX, $prevY);

    /**
     * Notify that a tower is attacking the antagonist.
     *
     * @param Tower $tower
     * @param Antagonist $antagonist
     * @return void
     */
    public function towerIsAttacking($tower, $antagonist);

    /**
     * Notify that a tower has just been build.
     *
     * @param Tower $tower
     * @return void
     */
    public function towerIsBuild($tower);
}
