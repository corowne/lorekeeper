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
        'item_id', 'quantity', 'stack_id'
    ];
    protected $table = 'user_items_log';
    public $timestamps = true;

    public function sender() 
    {
        return $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    public function recipient() 
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    public function item() 
    {
        return $this->belongsTo('App\Models\Item\Item');
    }

}
