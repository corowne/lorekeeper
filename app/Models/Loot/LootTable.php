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
    protected $table = 'loot_tables';
    
    public static $createRules = [
        'name' => 'required',
        'display_name' => 'required',
    ];
    
    public static $updateRules = [
        'name' => 'required',
        'display_name' => 'required',
    ];

    public function loot() 
    {
        return $this->hasMany('App\Models\Loot\Loot', 'loot_table_id');
    }
    
    public function getDisplayNameAttribute()
    {
        return '<span class="display-loot">'.$this->attributes['display_name'].'</span> '.add_help('This reward is random.');
    }

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
                if($roll > $count) $count += $l->weight;
                else 
                {
                    $result = $l;
                    break;
                }
                $prev = $l;
            }
            if(!$result) $result = $prev;

            // If this is chained to another loot table, roll on that table
            if($result->rewardable_type == 'LootTable') $rewards = mergeAssetsArrays($rewards, $result->reward->roll($result->quantity));
            else addAsset($rewards, $result->reward, $result->quantity);
        }

        return $rewards;
    }

    public function getAssetTypeAttribute()
    {
        return 'loot_tables';
    }

}
