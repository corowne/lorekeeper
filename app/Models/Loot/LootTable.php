<?php

namespace App\Models\Loot;

use Config;
use App\Models\Model;

class LootTable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loot_tables';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required',
        'display_name' => 'required',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required',
        'display_name' => 'required',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the loot data for this loot table.
     */
    public function loot() 
    {
        return $this->hasMany('App\Models\Loot\Loot', 'loot_table_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<span class="display-loot">'.$this->attributes['display_name'].'</span> '.add_help('This reward is random.');
    }

    /**
     * Gets the loot table's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'loot_tables';
    }

    /**********************************************************************************************
    
        OTHER FUNCTIONS

    **********************************************************************************************/
    
    /**
     * Rolls on the loot table and consolidates the rewards.
     *
     * @param  int  $quantity
     * @return \Illuminate\Support\Collection
     */
    public function roll($quantity = 1) 
    {
        $rewards = createAssetsArray();

        $loot = $this->loot;
        $totalWeight = 0;
        foreach($loot as $l) $totalWeight += $l->weight;

        for($i = 0; $i < $quantity; $i++)
        {
            $roll = mt_rand(0, $totalWeight - 1); 
            $result = null;
            $prev = null;
            $count = 0;
            foreach($loot as $l)
            { 
                $count += $l->weight;

                if($roll < $count)
                {
                    $result = $l;
                    break;
                }
                $prev = $l;
            }
            if(!$result) $result = $prev;

            if($result) {
                // If this is chained to another loot table, roll on that table
                if($result->rewardable_type == 'LootTable') $rewards = mergeAssetsArrays($rewards, $result->reward->roll($result->quantity));
                else addAsset($rewards, $result->reward, $result->quantity);
            }
        }
        return $rewards;
    }
}
