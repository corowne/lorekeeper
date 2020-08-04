<?php

namespace App\Models\ScavengerHunt;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class HuntParticipant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hunt_id', 'user_id', 'status', 'summary', 'locations', 'is_active',
        'start_at', 'end_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hunt_participants';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['start_at', 'end_at'];

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    

}
