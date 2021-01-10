<?php

namespace App\Models\Stats\User;

use Config;
use App\Models\Model;

class UserLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','current_level', 'current_exp', 'current_points'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_levels';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the shop stock.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }
    
    public function level() 
    {
        return $this->belongsTo('App\Models\Stats\User\Level', 'current_level');
    }

}