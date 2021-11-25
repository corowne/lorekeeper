<?php

namespace App\Models\Level;

use App;
use Config;
use App\Models\Model;

class UserLevelRequirement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'level_id', 'limit_type', 'limit_id', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_level_requirements';

    /**********************************************************************************************
    
        RELATIONS
    **********************************************************************************************/

    /**
     * Get the reward attached to the loot entry.
     */
    public function reward() 
    {
        switch ($this->limit_type)
        {
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'limit_id');
            case 'Currency':
                return $this->belongsTo('App\Models\Currency\Currency', 'limit_id');
            //case 'Recipe':
            //    return $this->belongsTo('App\Models\Recipe\Recipe', 'limit_id');
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo('App\Models\Level\UserLevelRequirement', 'limit_id', 'level_id')->whereNull('level_id');
        }
        return null;
    }
}