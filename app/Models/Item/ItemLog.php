<?php

namespace App\Models\Item;

use Config;
use App\Models\Model;

class ItemLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'recipient_id', 
        'log', 'log_type', 'data',
        'item_id', 'quantity', 'stack_id',
        'sender_type', 'recipient_type'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items_log';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who initiated the logged action.
     */
    public function sender() 
    {
        if($this->sender_type == 'User') return $this->belongsTo('App\Models\User\User', 'sender_id');
        if($this->sender_type == 'Shop') return $this->belongsTo('App\Models\Shop\UserShop', 'sender_id');
        return $this->belongsTo('App\Models\Character\Character', 'sender_id');
    }

    /**
     * Get the user who received the logged action.
     */
    public function recipient() 
    {
        if($this->recipient_type == 'User') return $this->belongsTo('App\Models\User\User', 'recipient_id');
        if($this->recipient_type == 'Shop') return $this->belongsTo('App\Models\Shop\UserShop', 'recipient_id');
        return $this->belongsTo('App\Models\Character\Character', 'recipient_id');
    }

    /**
     * Get the item that is the target of the action.
     */
    public function item() 
    {
        return $this->belongsTo('App\Models\Item\Item');
    }
}
