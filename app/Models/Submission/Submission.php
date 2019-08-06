<?php

namespace App\Models\Submission;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class Submission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prompt_id', 'user_id', 'staff_id', 'url',
        'comments', 'staff_comments', 
        'status', 'data'
    ];
    protected $table = 'submissions';
    public $timestamps = true;
    
    public static $createRules = [
        'url' => 'required',
    ];
    
    public static $updateRules = [
        'url' => 'required',
    ];
    
    
    public function prompt() 
    {
        return $this->belongsTo('App\Models\Prompt\Prompt', 'prompt_id');
    }
    
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    public function staff() 
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }
    
    public function characters() 
    {
        return $this->hasMany('App\Models\Submission\SubmissionCharacter', 'submission_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    public function scopeViewable($query, $user)
    {
        if($user && $user->hasPower('manage_submissions')) return $query;
        return $query->where(function($query) use ($user) {
            if($user) $query->where('user_id', $user->id)->orWhere('status', 'Approved');
            else $query->where('status', 'Approved');
        });
    }
    
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    public function getViewUrlAttribute()
    {
        return url('submissions/view/'.$this->id);
    }

    public function getAdminUrlAttribute()
    {
        return url('admin/submissions/edit/'.$this->id);
    }

    public function getRewardsAttribute()
    {
        $assets = parseAssetData($this->data);
        $rewards = [];
        foreach($assets as $type => $a)
        {
            $class = getAssetModelString($type, false);
            foreach($a as $id => $asset)
            {
                $rewards[] = (object)[
                    'rewardable_type' => $class,
                    'rewardable_id' => $id,
                    'quantity' => $asset['quantity']
                ];
            }
        }
        return $rewards;
    }
}
