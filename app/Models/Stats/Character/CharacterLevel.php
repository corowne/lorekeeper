<?php

namespace App\Models\Stats\Character;

use Config;
use App\Models\Model;

class CharacterLevel extends Model
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
    protected $table = 'level_characters';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'level' => 'required|unique:level_characters',
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
     * Get the rewards attached to this prompt.
     */
    public function rewards()
    {
        return $this->hasMany('App\Models\Stats\Character\CharacterLevelReward', 'level_id');
    }

    public function limits()
    {
        return $this->hasMany('App\Models\Stats\Character\CharacterLevelRequirement', 'level_id');
    }
}