<?php

namespace App\Models\Gallery;

use Settings;
use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class Gallery extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'parent_id', 'name', 'sort', 'description', 
        'currency_enabled', 'votes_required', 'submissions_open'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'galleries';
    
    /**
     * Validation rules for character creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:galleries|between:3,50',
        'description' => 'nullable',
    ];
    
    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,50',
        'description' => 'nullable',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the parent gallery.
     */
    public function parent() 
    {
        return $this->belongsTo('App\Models\Gallery\Gallery', 'parent_id');
    }

    /**
     * Get the child galleries of this gallery.
     */
    public function children() 
    {
        return $this->hasMany('App\Models\Gallery\Gallery', 'parent_id')->sort();
    }
    
    /**
     * Get the submissions made to this gallery.
     */
    public function submissions() 
    {
        return $this->hasMany('App\Models\Gallery\GallerySubmission', 'gallery_id')->visible()->orderBy('created_at', 'DESC');
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to return galleries sorted first by sort number and then name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query)
    {
        return $query->orderByRaw('ISNULL(sort), sort ASC')->orderBy('name', 'ASC');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-prompt">'.$this->name.'</a>';
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('gallery/'.$this->id);
    }

    /**
     * Gets whether or not the user can submit to the gallery.
     *
     * @return string
     */
    public function canSubmit($user = null)
    {
        if(Settings::get('gallery_submissions_open')) if($user && ($this->submissions_open || $user->hasPower('manage_submissions'))) return true;
        else return false;
    }

}
