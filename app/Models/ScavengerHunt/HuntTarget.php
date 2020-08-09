<?php

namespace App\Models\ScavengerHunt;

use Config;
use App\Models\Model;
use App\Models\Item\Item;

class HuntTarget extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hunt_id', 'target', 'item_id', 'quantity', 'page_id', 'description'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'scavenger_targets';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'item_id' => 'required',
        'quantity' => 'required|integer|min:1',
        'description' => 'nullable',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'item_id' => 'required',
        'quantity' => 'required|integer|min:1',
        'description' => 'nullable',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the item attached to the hunt target.
     */
    public function reward() 
    {
        return $this->belongsTo('App\Models\Item\Item', 'item_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the target item and its quantity.
     *
     * @return string
     */
    public function getDisplayItemAttribute()
    {
        $item = Item::find($this->item_id);
        $image = ($item->imageUrl) ? '<img class="small-icon" src="'.$item->imageUrl.'">' : null;
        return $image.' '.$item->displayName.' x'.$this->attributes['quantity'];
    }

}
