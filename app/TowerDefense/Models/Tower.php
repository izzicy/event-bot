<?php

namespace App\TowerDefense\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $health
 * @property int $x
 * @property int $y
 */
class Tower extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tdg_towers';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
