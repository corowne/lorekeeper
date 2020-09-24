<?php

namespace App\Models\Gallery;

use Config;
use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Model;

class GallerySubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'gallery_id', 
        'hash', 'extension', 'text', 'parsed_text',
        'title', 'description', 'parsed_description',
        'prompt_id', 'data', 
        'is_visible', 'status', 'vote_data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gallery_submissions';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for character creation.
     *
     * @var array
     */
    public static $createRules = [
        'title' => 'required|between:3,200',
        'image' => 'required_without:text|mimes:png,jpeg,gif|max:4000',
        'text' => 'required_without:image',
        'description' => 'nullable',
    ];
    
    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'title' => 'required|between:3,200',
        'description' => 'nullable',
        'image' => 'mimes:png,jpeg,gif|max:4000'
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the user who made the submission.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**
     * Get the user who made the submission.
     */
    public function collaborators() 
    {
        return $this->hasMany('App\Models\Gallery\GalleryCollaborator', 'gallery_submission_id');
    }

    /**
     * Get the user who made the submission.
     */
    public function characters() 
    {
        return $this->hasMany('App\Models\Gallery\GalleryCharacter', 'gallery_submission_id');
    }

    /**
     * Get the gallery this submission is in.
     */
    public function gallery() 
    {
        return $this->belongsTo('App\Models\Gallery\Gallery', 'gallery_id');
    }

    /**
     * Get the prompt this submission is for if relevant.
     */
    public function prompt() 
    {
        return $this->belongsTo('App\Models\Prompt\Prompt', 'prompt_id');
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
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include submissions that are pending,
     * and where all collaborators have approved the particulars.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'Pending')->whereHas('gallery_submission_collaborators', function($q) {
            $q->where('has_approved', 1);
        });
    }

    /**
     * Scope a query to only include accepted submissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'Accepted');
    }

    /**
     * Scope a query to only include rejected submissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    /**
     * Scope a query to only include submissions the user has either submitted or collaborated on.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserSubmissions($query)
    {
        return $query->where('user_id', Auth::user()->id)->orWhereIn('id', GalleryCollaborator::where('user_id', Auth::user()->id)->pluck('gallery_submission_id')->toArray());
    }

    /**
     * Scope a query to only include submissions visible within the gallery.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user)
    {
        if($user && $user->hasPower('manage_submissions')) return $query->where('status', 'Accepted');
        return $query->where('status', 'Accepted')->where('is_visible', 1);
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/gallery/'.floor($this->id / 1000);
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '_'.$this->hash.'.'.$this->extension;
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if(!isset($this->hash)) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the file name of the model's thumbnail image.
     *
     * @return string
     */
    public function getThumbnailFileNameAttribute()
    {
        return $this->id . '_'.$this->hash.'_th.'.$this->extension;
    }

    /**
     * Gets the path to the file directory containing the model's thumbnail image.
     *
     * @return string
     */
    public function getThumbnailPathAttribute()
    {
        return $this->imagePath;
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        if(!isset($this->hash)) return null;
        return asset($this->imageDirectory . '/' . $this->thumbnailFileName);
    }

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
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getVoteDataAttribute()
    {
        return json_decode($this->attributes['vote_data'], true);
    }

    /**
     * Get the viewing URL of the submission/claim.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'">'.$this->name.'</a>';
    }

    /**
     * Get the viewing URL of the submission/claim.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('gallery/view/'.$this->id);
    }
    
}
