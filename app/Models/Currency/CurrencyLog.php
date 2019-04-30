<?php

namespace App\Models\Currency;

use Config;
use App\Models\Model;

class CurrencyLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'sender_type',
        'recipient_id', 'recipient_type',
        'log', 'log_type', 'data',
        'currency_id', 'quantity'
    ];
    protected $table = 'currencies_log';
    public $timestamps = true;

    public function sender() 
    {
        if($this->sender_type == 'User') return $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    public function recipient() 
    {
        if($this->recipient_type == 'User') return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    public function currency() 
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }

    public function displayRow($user) 
    {
        $ret = '<tr class="'.($this->recipient_id == $user->id ? 'inflow' : 'outflow').'">';
        $ret .= '<td>'.$this->sender->displayName.'</td>';
        $ret .= '<td>'.$this->recipient->displayName.'</td>';
        $ret .= '<td>'.($this->recipient_id == $user->id ? '+' : '-').' '.$this->currency->display(abs($this->quantity)).'</td>';
        $ret .= '<td>'.$this->log.'</td>';
        $ret .= '<td>'.format_date($this->created_at).'</td>';
        return $ret . '</tr>';
    }

}
