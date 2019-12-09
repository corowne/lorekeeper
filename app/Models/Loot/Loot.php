<?php

namespace App\Models\Loot;

use Config;
use App\Models\Model;

class Loot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loot_table_id', 'rewardable_type', 'rewardable_id', 'quantity', 'weight'
    ];
    protected $table = 'loots';
    
    public static $createRules = [
        'rewardable_type' => 'required',
        'rewardable_id' => 'required',
        'quantity' => 'required|integer|min:1',
        'weight' => 'required|integer|min:1',
    ];
    
    public static $updateRules = [
        'rewardable_type' => 'required',
        'rewardable_id' => 'required',
        'quantity' => 'required|integer|min:1',
        'weight' => 'required|integer|min:1',
    ];
    
    public function reward() 
    {
        switch ($this->rewardable_type)
        {
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'rewardable_id');
            case 'Currency':
                return $this->belongsTo('App\Models\Currency\Currency', 'rewardable_id');
            case 'LootTable':
                return $this->belongsTo('App\Models\Loot\LootTable', 'rewardable_id');
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo('App\Models\Loot\Loot', 'rewardable_id', 'loot_table_id')->whereNull('loot_table_id');
        }
        return null;
    }
}
