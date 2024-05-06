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
        'encounter_id', 'name', 'result', 'output', 'extras'
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
        'name' => 'required|between:3,50',
        'result' => 'required',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,50',
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

        /**
     * Get the required items / assets to perform the prompt.
     */
    public function limits()
    {
        return $this->hasMany('App\Models\Encounter\PromptLimit');
    }

     /**
     * Gets the decoded output json
     *
     * @return array
     */
    public function getRewardsAttribute()
    {
        $rewards = [];
        if($this->output) {
            $assets = $this->getRewardItemsAttribute();

            foreach($assets as $type => $a)
            {
                $class = getAssetModelString($type, false);
                foreach($a as $id => $asset)
                {
                    $rewards[] = (object)[
                        'rewardable_type' => $class,
                        'rewardable_id' => $id,
                        'quantity' => $asset['quantity']
                    ];
                }
            }
        }
        return $rewards;
    }

    /**
     * Interprets the json output and retrieves the corresponding items
     *
     * @return array
     */
    public function getRewardItemsAttribute()
    {
        return parseAssetData(json_decode($this->output, true));
    }

        /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getExtrasAttribute()
    {
        if (!$this->id) return null;
        return json_decode($this->attributes['extras'], true);
    }
 
}
