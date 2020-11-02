<?php

namespace App\Models;

use Carbon\Carbon;
use Config;
use App\Models\Model;
use App\Traits\Commentable;

class Sales extends Model
{
    use Commentable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'text', 'parsed_text', 'title', 'is_visible', 'post_at', 
        'is_open', 'comments_open_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['post_at', 'comments_open_at'];

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'title' => 'required|between:3,25',
        'text' => 'required',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'title' => 'required|between:3,25',
        'text' => 'required',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the user who created the Sales post.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include visible posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->orderBy('updated_at', 'DESC')->where('is_visible', 1);
    }

    /**
     * Scope a query to only include posts that are scheduled to be posted and are ready to post.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShouldBeVisible($query)
    {
        return $query->whereNotNull('post_at')->where('post_at', '<', Carbon::now())->where('is_visible', 0);
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the Sales slug.
     *
     * @return bool
     */
    public function getSlugAttribute()
    {
        return $this->id . '.' . str_slug($this->title);
    }

    /**
     * Displays the Sales post title, linked to the Sales post itself.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'"> ['.($this->is_open ? (isset($this->comments_open_at) && $this->comments_open_at > Carbon::now() ? 'Preview' : 'Open') : 'Closed').'] '.$this->title.'</a>';
    }

    /**
     * Gets the Sales post URL.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('sales/'.$this->slug);
    }
}
