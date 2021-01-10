<?php

namespace App\Models\Stats\Character;

use Config;
use App\Models\Model;

class CharacterStat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'stat_id', 'stat_level', 'count', 'current_count'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_stats';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function stat() 
    {
        return $this->belongsTo('App\Models\Stats\Character\Stat');
    }
    
}