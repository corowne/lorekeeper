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
        return $this->belongsTo('App\Models\Character\Character', 'sender_id');
    }

    public function recipient() 
    {
        if($this->recipient_type == 'User') return $this->belongsTo('App\Models\User\User', 'recipient_id');
        return $this->belongsTo('App\Models\Character\Character', 'recipient_id');
    }

    public function currency() 
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }

}
