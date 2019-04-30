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

    public function displayRow($user) 
    {
        $ret = '<tr class="'.($this->recipient_id == $user->id ? 'inflow' : 'outflow').'">';
        $ret .= '<td>'.($this->sender ? $this->sender->displayName : '').'</td>';
        $ret .= '<td>'.($this->recipient ? $this->recipient->displayName : '').'</td>';
        $ret .= '<td><a href="'.$this->item->searchUrl.'">'.$this->item->name.'</a> (×'.$this->quantity.')</td>';
        $ret .= '<td>'.$this->log.'</td>';
        $ret .= '<td>'.format_date($this->created_at).'</td>';
        return $ret . '</tr>';
    }

}
