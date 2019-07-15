<?php

namespace App\Models\Prompt;

use Config;
use App\Models\Model;

class PromptReward extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prompt_id', 'rewardable_type', 'rewardable_id', 'quantity'
    ];
    protected $table = 'prompt_rewards';
    
    public static $createRules = [
        'rewardable_type' => 'required',
        'rewardable_id' => 'required',
        'quantity' => 'required|integer|min:1',
    ];
    
    public static $updateRules = [
        'rewardable_type' => 'required',
        'rewardable_id' => 'required',
        'quantity' => 'required|integer|min:1',
    ];
    
    public function reward() 
    {
        switch ($this->rewardable_type)
        {
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'rewardable_id');
                break;
            case 'Currency':
                return $this->belongsTo('App\Models\Currency\Currency', 'rewardable_id');
                break;
            case 'LootTable':
                return $this->belongsTo('App\Models\Loot\LootTable', 'rewardable_id');
                break;
        }
        return null;
    }
}
