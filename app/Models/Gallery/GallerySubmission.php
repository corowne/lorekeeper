<?php

namespace App\Models\Gallery;

use Config;
use DB;
use Settings;
use Carbon\Carbon;
use App\Models\Currency\Currency;
use App\Models\Prompt\Prompt;
use App\Models\Submission\Submission;
use App\Models\Model;

use App\Traits\Commentable;

class GallerySubmission extends Model
{
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'gallery_id', 'hash', 'extension',
        'text', 'parsed_text', 'content_warning',
        'title', 'description', 'parsed_description',
        'prompt_id', 'data', 'is_visible', 'status',
        'vote_data', 'staff_id', 'is_valued',
        'staff_comments', 'parsed_staff_comments'
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
        'image' => 'required_without:text|mimes:png,jpeg,gif|max:3000',
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
        'image' => 'mimes:png,jpeg,gif|max:3000'
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
     * Get the staff member who last edited the submission's comments.
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    /**
     * Get the collaborating users on the submission.
     */
    public function collaborators()
    {
        return $this->hasMany('App\Models\Gallery\GalleryCollaborator', 'gallery_submission_id')->where('type', 'Collab');
    }

    /**
     * Get the user(s) who are related to the submission in some way.
     */
    public function participants()
    {
        return $this->hasMany('App\Models\Gallery\GalleryCollaborator', 'gallery_submission_id')->where('type', '!=', 'Collab');
    }

    /**
     * Get the characters associated with the submission.
     */
    public function characters()
    {
        return $this->hasMany('App\Models\Gallery\GalleryCharacter', 'gallery_submission_id');
    }

    /**
     * Get any favorites on the submission.
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
     * Scope a query to only include submissions that require currency awards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresAward($query)
    {
        if(!Settings::get('gallery_submissions_reward_currency')) return $query->whereNull('id');
        return $query->where('status', 'Accepted')->whereIn('gallery_id', Gallery::where('currency_enabled', 1)->pluck('id')->toArray());
    }

    /**
     * Scope a query to only include submissions the user has either submitted or collaborated on.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param                                         $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserSubmissions($query, $user)
    {
        return $query->where('user_id', $user->id)->orWhereIn('id', GalleryCollaborator::where('user_id', $user->id)->where('type', 'Collab')->pluck('gallery_submission_id')->toArray());
    }

    /**
     * Scope a query to only include submissions visible within the gallery.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user = null)
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
     * Gets the voting data of the gallery submission.
     *
     * @return string
     */
    public function getVoteDataAttribute()
    {
        return collect(json_decode($this->attributes['vote_data'], true));
    }

    /**
     * Get the title of the submission, with prefix.
     *
     * @return string
     */
    public function getDisplayTitleAttribute()
    {
        return $this->prefix.$this->attributes['title'];
    }

    /**
     * Get the display name of the submission.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'">'.$this->displayTitle.'</a>';
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
     * Checks if all of a submission's collaborators have approved or no.
     *
     * @return string
     */
    public function getPrefixAttribute()
    {
        $currencyName = Currency::find(Settings::get('group_currency'))->abbreviation ? Currency::find(Settings::get('group_currency'))->abbreviation : Currency::find(Settings::get('group_currency'))->name;

        $prefixList = [];
        if($this->promptSubmissions->count()) foreach($this->prompts as $prompt) isset($prompt->prefix) ? ($prefixList[] = $prompt->prefix) : null;
        foreach($this->participants as $participant) {
            switch($participant->type) {
                case 'Collab':
                    $prefixList[] = 'Collab';
                    break;
                case 'Trade':
                    $prefixList[] = 'Trade';
                    break;
                case 'Gift':
                    $prefixList[] = 'Gift';
                    break;
                case 'Comm':
                    $prefixList[] = 'Comm';
                    break;
                case 'Comm (Currency)':
                    $prefixList[] = 'Comm ('.$currencyName.')';
                    break;
            }
        }
        if($prefixList != null) return '['.implode(' : ', array_unique($prefixList)).'] ';
        return null;
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

    /**
     * Get the users responsible for the submission (submitting user or collaborators).
     *
     * @return string
     */
    public function getCreditsPlainAttribute()
    {
        if($this->collaborators->count()) {
            foreach($this->collaborators as $count=>$collaborator) {
                $collaboratorList[] = $collaborator->user->name;
            }
            return implode(', ', $collaboratorList);
        }
        else return $this->user->name;
    }

    /**
     * Checks if all of a submission's collaborators have approved or no.
     *
     * @return string
     */
    public function getCollaboratorApprovedAttribute()
    {
        if($this->collaborators->where('has_approved', 0)->count()) return false;
        return true;
    }

    /**
     * Gets prompt submissions associated with this gallery submission.
     *
     * @return array
     */
    public function getPromptSubmissionsAttribute()
    {
        // Only returns submissions which are viewable to everyone,
        // but given that this is for the sake of public display, that's fine
        return Submission::viewable()->whereNotNull('prompt_id')->where('url', $this->url)->get();
    }

    /**
     * Gets prompts associated with this gallery submission.
     *
     * @return array
     */
    public function getPromptsAttribute()
    {
        // Only returns submissions which are viewable to everyone,
        // but given that this is for the sake of public display, that's fine
        return Prompt::whereIn('id', $this->promptSubmissions->pluck('prompt_id'))->get();
    }

    /**
     * Gets the excerpt of text for a literature submission.
     *
     * @return string
     */
    public function getExcerptAttribute()
    {
        if(!isset($this->parsed_text)) return null;
        else return strip_tags(substr($this->parsed_text, 0, 500)).(strlen($this->parsed_text) > 500 ? '...' : '');
    }

}
