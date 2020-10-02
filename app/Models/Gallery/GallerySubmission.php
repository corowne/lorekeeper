<?php

namespace App\Models\Gallery;

use Config;
use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Model;

use Laravelista\Comments\Commentable;

class GallerySubmission extends Model
{
    use Commentable;

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
     * Get the user who made the submission.
     */
    public function favorites() 
    {
        return $this->hasMany('App\Models\Gallery\GalleryFavorite', 'gallery_submission_id');
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
     * Scope a query to only include submissions where all collaborators have approved.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCollaboratorApproved($query)
    {
        return $query->whereNotIn('id', GalleryCollaborator::where('has_approved', 0)->pluck('gallery_submission_id')->toArray());
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
     * Scope a query to only include submissions that require currecy awards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresAward($query)
    {
        return $query->where('status', 'Accepted');
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
    public function scopeVisible($query)
    {
        if(Auth::check() && Auth::user()->hasPower('manage_submissions')) return $query->where('status', 'Accepted');
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
     * Gets the gallery submission's thumbnail-- the image if there is one, truncated text if not.
     *
     * @return string
     */
    public function getThumbnailAttribute()
    {
        if(isset($this->hash)) return '<img class="img-thumbnail" src="'.$this->thumbnailUrl.'"/>';
        return 
        '<div class="mx-auto img-thumbnail text-left" style="height:'.Config::get('lorekeeper.settings.masterlist_thumbnails.height').'px; width:'.Config::get('lorekeeper.settings.masterlist_thumbnails.width').'px;">
            <span class="badge-primary px-2 py-1" style="border-radius:0 0 .5em 0; position:absolute; z-index:5;">Literature</span>
            <div class="container-'.$this->id.' parsed-text pb-2 pr-2" style="height:'.(Config::get('lorekeeper.settings.masterlist_thumbnails.height')-10).'px; width:'.(Config::get('lorekeeper.settings.masterlist_thumbnails.width')-4).'px; overflow:hidden;">
                <div class="content-'.$this->id.' text-body">'.$this->parsed_text.'</div>
            </div>
        </div>
        <style>
            .content-'.$this->id.' {transition-duration: '.(strlen($this->parsed_text)/1000).'s;}
            .content-'.$this->id.':hover, .content-'.$this->id.':focus-within {transform: translateY(calc('.Config::get('lorekeeper.settings.masterlist_thumbnails.height').'px - 100%)); transition-duration: '.(strlen($this->parsed_text)/100).'s;}
        </style>';
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
     * Gets the voting data of the gallery submission.
     *
     * @return string
     */
    public function getVoteDataAttribute()
    {
        return collect(json_decode($this->attributes['vote_data'], true));
    }

    /**
     * Get the viewing URL of the submission/claim.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'">'.$this->title.'</a>';
    }

    /**
     * Get the viewing URL of the submission.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('gallery/view/'.$this->id);
    }

    /**
     * Get the internal processing URL of the submission.
     *
     * @return string
     */
    public function getQueueUrlAttribute()
    {
        return url('gallery/queue/'.$this->id);
    }

    /**
     * Get whether or not the submission is generally viewable.
     *
     * @return bool
     */
    public function getIsVisibleAttribute()
    {
        if($this->attributes['is_visible'] && $this->status == 'Accepted') return true;
    }

    /**
     * Get the users responsible for the submission (submitting user or collaborators).
     *
     * @return string
     */
    public function getCreditsAttribute()
    {
        if($this->collaborators->count()) {
            foreach($this->collaborators as $count=>$collaborator) {
                $collaboratorList[] = $collaborator->user->displayName;
            }
            return implode(', ', $collaboratorList);
        }
        else return $this->user->displayName;
    }
    
}
