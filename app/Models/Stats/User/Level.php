<?php

namespace App\Models\Stats\User;

use Config;
use App\Models\Model;

class Level extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'level', 'exp_required','stat_points', 'description'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'level_users';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'level' => 'required|unique:level_users',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'level' => 'required',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the rewards attached to this level.
     */
    public function rewards()
    {
        return $this->hasMany('App\Models\Stats\User\UserLevelReward', 'level_id');
    }
    
    public function limits()
    {
        return $this->hasMany('App\Models\Stats\User\UserLevelRequirement');
    }
}