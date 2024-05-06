<?php

namespace App\Models\Encounter;

use Config;
use App\Models\Model;

class PromptLimit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['encounter_prompt_id', 'item_id', 'item_type'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encounter_prompt_limits';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the reward attached to the loot entry.
     */
    public function item()
    {
        switch ($this->item_type) {
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'item_id');
            case 'Currency':
                return $this->belongsTo('App\Models\Currency\Currency', 'item_id');
            /**case 'Recipe':
                return $this->belongsTo('App\Models\Recipe\Recipe', 'item_id');
            case 'Pet':
                return $this->belongsTo('App\Models\Pet\Pet', 'item_id');
            case 'Award':
                return $this->belongsTo('App\Models\Award\Award', 'item_id');
            case 'Gear':
                return $this->belongsTo('App\Models\Claymore\Gear', 'item_id');
            case 'Weapon':
                return $this->belongsTo('App\Models\Claymore\Weapon', 'item_id');
            case 'Enchantment':
                return $this->belongsTo('App\Models\Claymore\Enchantment', 'item_id');
            case 'Recipe':
                return $this->belongsTo('App\Models\Recipe\Recipe', 'item_id');
            case 'Collection':
                return $this->belongsTo('App\Models\Collection\Collection', 'item_id');**/
        }
        return null;
    }
}
