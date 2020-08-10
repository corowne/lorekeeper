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
    protected $table = 'scavenger_participants';

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
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the participating user.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    /**
     * Get the hunt being participated in.
     */
    public function hunt() 
    {
        return $this->belongsTo('App\Models\ScavengerHunt\ScavengerHunt', 'hunt_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Get the item data that will be added to the stack as a record of its source.
     *
     * @return string
     */
    public function getItemDataAttribute()
    {
        return 'Claimed from '.$this->hunt->displayLink.' by '.$this->user->displayName.'.';
    }

    /**
     * Get the number of targets the user has claimed.
     *
     * @return int
     */
    public function getTargetsCountAttribute()
    {
        $found = 0;
        foreach([$this['target_1'], $this['target_2'], $this['target_3'], $this['target_4'], $this['target_5'],
        $this['target_6'], $this['target_7'], $this['target_8'], $this['target_9'], $this['target_10']] as $timestamp) {
            if(isset($timestamp)) {
                $found += 1;
            }
        }
        return $found;
    }

}
