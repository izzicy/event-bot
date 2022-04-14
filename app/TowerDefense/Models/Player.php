<?php

namespace App\TowerDefense;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $money
 * @property int $score
 */
class Player extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tdg_players';
}
