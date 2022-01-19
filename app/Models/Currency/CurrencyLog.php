<?php

namespace App\Models\Currency;

use App\Models\Model;

class CurrencyLog extends Model
{
    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'sender_type',
        'recipient_id', 'recipient_type',
        'log', 'log_type', 'data',
        'currency_id', 'quantity',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currencies_log';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who initiated the logged action.
     */
    public function sender()
    {
        if ($this->sender_type == 'User') {
            return $this->belongsTo('App\Models\User\User', 'sender_id');
        }

        return $this->belongsTo('App\Models\Character\Character', 'sender_id');
    }

    /**
     * Get the user who received the logged action.
     */
    public function recipient()
    {
        if ($this->recipient_type == 'User') {
            return $this->belongsTo('App\Models\User\User', 'recipient_id');
        }

        return $this->belongsTo('App\Models\Character\Character', 'recipient_id');
    }

    /**
     * Get the currency that is the target of the action.
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }
}
