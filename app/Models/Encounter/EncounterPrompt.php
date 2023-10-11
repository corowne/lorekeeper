<?php

namespace App\Models\Encounter;

use Config;
use App\Models\Model;

class EncounterPrompt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'encounter_id', 'name', 'result', 'give_reward'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encounter_prompts';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required',
        'result' => 'required',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required',
        'result' => 'required',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the reward attached to the loot entry.
     */
    public function encounter()
    {
        return $this->belongsTo('App\Models\Encounter\Encounter', 'encounter_id');
    }
 
}
