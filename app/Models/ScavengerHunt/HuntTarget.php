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

    /**
     * Get the target's parent hunt.
     */
    public function hunt() 
    {
        return $this->belongsTo('App\Models\ScavengerHunt\ScavengerHunt', 'hunt_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Gets the target's number within the hunt.
     *
     * @return string
     */
    public function getTargetNumberAttribute()
    {
        return $this->hunt->numberedTargets[$this->id] + 1;
    }

    /**
     * Gets the target's field for participant logging.
     *
     * @return string
     */
    public function getTargetFieldAttribute()
    {
        switch($this->targetNumber) {
            default:
            flash('Invalid target number.')->error();
            break;
        case 1:
            return 'target_1';
            break;
        case 2:
            return 'target_2';
            break;
        case 3:
            return 'target_3';
            break;
        case 4:
            return 'target_4';
            break;
        case 5:
            return 'target_5';
            break;
        case 6:
            return 'target_6';
            break;
        case 7:
            return 'target_7';
            break;
        case 8:
            return 'target_8';
            break;
        case 9:
            return 'target_9';
            break;
        case 10:
            return 'target_10';
            break;
        }
    }

    /**
     * Gets if the target has been claimed.
     *
     * @return string
     */
    public function getIsClaimedAttribute()
    {
        $participant = HuntParticipant::where([
            ['user_id', '=', Auth::user()->id],
            ['hunt_id', '=', $hunt->id],
        ])->first();
        return url('hunts/targets/'.$this->page_id);
    }

    /**
     * Displays the target item and its quantity.
     *
     * @return string
     */
    public function getDisplayItemAttribute()
    {
        $image = ($this->item->imageUrl) ? '<img class="small-icon" src="'.$this->item->imageUrl.'"/>' : null;
        return $image.' '.$this->item->displayName.' ×'.$this->attributes['quantity'];
    }

    /**
     * Displays the target item and its quantity.
     *
     * @return string
     */
    public function getDisplayItemLongAttribute()
    {
        $image = ($this->item->imageUrl) ? '<img style="max-height:150px;" src="'.$this->item->imageUrl.'" data-toggle="tooltip" title="'.$this->item->name.'"/>' : null;
        return $image.(isset($image) ? '<br/>' : '').' '.$this->item->displayName.' ×'.$this->attributes['quantity'];
    }

    /**
     * Displays the target item.
     *
     * @return string
     */
    public function getDisplayItemShortAttribute()
    {
        $image = ($this->item->imageUrl) ? '<img style="max-height:150px;" src="'.$this->item->imageUrl.'" data-toggle="tooltip" title="'.$this->item->name.'"/>' : null;
        if(isset($image)) return $image;
        else return $this->item->displayName;
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
