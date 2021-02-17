<?php

namespace App\Models\Stats;

use Config;
use App\Models\Model;

class CountLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'recipient_id', 
        'log', 'quantity', 'log_type', 'data',
        'sender_type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'count_log';

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
        return $this->belongsTo('App\Models\Character\Character', 'sender_id');
    }
}
