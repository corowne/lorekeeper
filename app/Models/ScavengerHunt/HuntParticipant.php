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
        'hunt_id', 'user_id', 
        'target_1', 'target_2', 'target_3', 'target_4', 'target_5',
        'target_6', 'target_7', 'target_8', 'target_9', 'target_10'
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
    public $dates = ['target_1', 'target_2', 'target_3', 'target_4', 'target_5',
    'target_6', 'target_7', 'target_8', 'target_9', 'target_10'];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    

}
