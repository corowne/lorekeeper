<?php

namespace App\Models\Stats\Character;

use Config;
use App\Models\Model;

class CharaLevels extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id','current_level', 'current_exp', 'current_points'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_levels';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the shop stock.
     */
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function level() 
    {
        return $this->belongsTo('App\Models\Stats\Character\CharacterLevel', 'current_level');
    }
    
}