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
    public function item() 
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
        $image = ($this->item->imageUrl) ? '<img class="small-icon" src="'.$this->item->imageUrl.'">' : null;
        return $image.' '.$this->item->displayName.' x'.$this->attributes['quantity'];
    }

    /**
     * Gets the target's public-facing link.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('hunts/targets/'.$this->page_id);
    }

    /**
     * Formats a link for public display of the target.
     *
     * @return string
     */
    public function getDisplayLinkAttribute()
    {
        $image = ($this->item->imageUrl) ? '<img src="'.$this->item->imageUrl.'" alt="'.$this->item->name.'" />' : $this->item->name;
        return '<a href="'.$this->url.'">'.$image.'</a>';
    }

    /**
     * Formats a wiki link for public display of the target.
     *
     * @return string
     */
    public function getWikiLinkAttribute()
    {
        $image = ($this->item->imageUrl) ? $this->item->imageUrl : $this->item->name;
        return '['.$this->url.' '.$image.']';
    }

}
