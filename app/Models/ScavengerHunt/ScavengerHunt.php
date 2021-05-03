<?php

namespace App\Models\ScavengerHunt;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class ScavengerHunt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'summary', 'locations', 'clue', 'start_at', 'end_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'scavenger_hunts';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['start_at', 'end_at'];
    
    /**
     * Validation rules for hunt creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:scavenger_hunts|between:3,50',
        'display_name' => 'required|between:3,40',
        'summary' => 'nullable',
        'locations' => 'nullable',
    ];
    
    /**
     * Validation rules for hunt updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,50',
        'display_name' => 'required|between:3,40',
        'summary' => 'nullable',
        'locations' => 'nullable',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the targets attached to this scavenger hunt.
     */
    public function targets() 
    {
        return $this->hasMany('App\Models\ScavengerHunt\HuntTarget', 'hunt_id');
    }

    /**
     * Get the targets attached to this scavenger hunt.
     */
    public function participants() 
    {
        return $this->hasMany('App\Models\ScavengerHunt\HuntParticipant', 'hunt_id');
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include active hunts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where(function($query) {
            $query->whereNull('start_at')->orWhere('start_at', '<', Carbon::now())->orWhere(function($query) {
                $query->where('start_at', '>=', Carbon::now());
            });
        })->where(function($query) {
                $query->whereNull('end_at')->orWhere('end_at', '>', Carbon::now())->orWhere(function($query) {
                    $query->where('end_at', '<=', Carbon::now());
                });
        });
        
    }

    /**
     * Scope a query to sort hunts in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort hunts by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort hunts oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort hunts by start date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortStart($query, $reverse = false)
    {
        return $query->orderBy('start_at', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort hunts by end date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortEnd($query, $reverse = false)
    {
        return $query->orderBy('end_at', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to get participants of a particular hunt.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParticipants($query)
    {
        $query->select('hunt_participants.*')->where('hunt_id', $this->id);
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the model's name, linked to its page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->attributes['display_name'];
    }
    
    /**
     * Displays the model's name, linked to its page.
     *
     * @return string
     */
    public function getDisplayLinkAttribute()
    {
        return '<a href="'.$this->url.'" class="display-prompt">'.$this->attributes['display_name'].'</a>';
    }

    /**
     * Gets the URL of the model's page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('hunts/'.$this->id);
    }

    /**
     * Check if the hunt is active or not.
     *
     * @return string
     */
    public function getIsActiveAttribute()
    {
        if($this->start_at <= Carbon::now() && $this->end_at >= Carbon::now())
            return TRUE;
        else
            return FALSE;
        
    }

    /**
     * Get the list of targets attached to this scavenger hunt, keyed to their number within the hunt.
     * 
     * @return array
     */
    public function getNumberedTargetsAttribute() 
    {
        $targets = (array_flip($this->targets->pluck('id')->toArray()));
        return $targets;
    }

}
