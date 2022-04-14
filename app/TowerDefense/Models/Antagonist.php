<?php

namespace App\TowerDefense;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $health
 * @property int $x
 * @property int $y
 */
class Antagonist extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tdg_antagonists';
}
