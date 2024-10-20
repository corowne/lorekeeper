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
        'comments', 'staff_comments', 'parsed_staff_comments',
        'status', 'data', 'focus_chara_id', 'bonus'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'submissions';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for submission creation.
     *
     * @var array
     */
    public static $createRules = [
        'url' => 'nullable|url',
    ];

    /**
     * Validation rules for submission updating.
     *
     * @var array
     */
    public static $updateRules = [
        'url' => 'nullable|url',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the prompt this submission is for.
     */
    public function prompt()
    {
        return $this->belongsTo('App\Models\Prompt\Prompt', 'prompt_id');
    }

    /**
     * Get the user who made the submission.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**
     * Get the staff who processed the submission.
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    /**
     * Get the characters attached to the submission.
     */
    public function characters()
    {
        return $this->hasMany('App\Models\Submission\SubmissionCharacter', 'submission_id');
    }

    /**
     * Get the  focus chara attached to the submission.
     */
    public function focus()
    {
        return $this->belongsTo('App\Models\Character\Character', 'focus_chara_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include pending submissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include viewable submissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViewable($query, $user = null)
    {
        $forbiddenSubmissions = $this
        ->whereHas('prompt', function($q) {
            $q->where('hide_submissions', 1)->whereNotNull('end_at')->where('end_at', '>', Carbon::now());
        })
        ->orWhereHas('prompt', function($q) {
            $q->where('hide_submissions', 2);
        })
        ->orWhere('status', '!=', 'Approved')->pluck('id')->toArray();

        if($user && $user->hasPower('manage_submissions')) return $query;
        else return $query->where(function($query) use ($user, $forbiddenSubmissions) {
            if($user) $query->whereNotIn('id', $forbiddenSubmissions)->orWhere('user_id', $user->id);
            else $query->whereNotIn('id', $forbiddenSubmissions);
        });
    }

    /**
     * Scope a query to sort submissions oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort submissions by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    /**
     * Gets the inventory of the user for selection.
     *
     * @return array
     */
    public function getInventory($user)
    {
        return $this->data && isset($this->data['user']['user_items']) ? $this->data['user']['user_items'] : [];
        return $inventory;
    }

    /**
     * Gets the currencies of the given user for selection.
     *
     * @param  \App\Models\User\User $user
     * @return array
     */
    public function getCurrencies($user)
    {
        return $this->data && isset($this->data['user']) && isset($this->data['user']['currencies']) ? $this->data['user']['currencies'] : [];
    }

    /**
     * Get the viewing URL of the submission/claim.
     *
     * @return string
     */
    public function getViewUrlAttribute()
    {
        return url(($this->prompt_id ? 'submissions' : 'claims') . '/view/'.$this->id);
    }

    /**
     * Get the admin URL (for processing purposes) of the submission/claim.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/' . ($this->prompt_id ? 'submissions' : 'claims') . '/edit/'.$this->id);
    }

    /**
     * Get the rewards for the submission/claim.
     *
     * @return array
     */
    public function getRewardsAttribute()
    {
        if(isset($this->data['rewards']))
        $assets = parseAssetData($this->data['rewards']);
        else
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
