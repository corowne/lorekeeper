<?php

namespace App\Models\Level;

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
        'level', 'exp_required', 'stat_points', 'description', 'level_type'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'levels';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'level' => 'required',
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
        if($this->level_type == 'User')
            return $this->hasMany('App\Models\Level\UserLevelReward', 'level_id');
        else
        return $this->hasMany('App\Models\Level\CharacterLevelReward', 'level_id');
    }
    
    public function limits()
    {
        if ($this->level_type == 'User')
            return $this->hasMany('App\Models\Level\UserLevelRequirement', 'level_id');
        else
            return $this->hasMany('App\Models\Level\CharacterLevelRequirement', 'level_id');
    }
}