<?php

namespace App\Models\Rank;

use App\Models\Model;

class RankPower extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rank_id', 'power',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rank_powers';
}
