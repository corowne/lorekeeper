<?php

namespace App\Models;
use App\Models\Model;

use App\Events\CommentCreated;
use App\Events\CommentUpdated;
use App\Events\CommentDeleted;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Comment extends Model
{
	use SoftDeletes;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'commenter'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 'approved', 'guest_name', 'guest_email', 'is_featured', 'type'
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'approved' => 'boolean'
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => CommentCreated::class,
        'updated' => CommentUpdated::class,
        'deleted' => CommentDeleted::class,
    ];

    /**
     * The user who posted the comment.
     */
    public function commenter()
    {
        return $this->morphTo();
    }

    /**
     * The model that was commented upon.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Returns all comments that this comment is the parent of.
     */
    public function children()
    {
        return $this->hasMany('App\Models\Comment', 'child_id');
    }

    /**
     * Returns the comment to which this comment belongs to.
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Comment', 'child_id');
    }

    /**
     * Gets / Creates permalink for comments - allows user to go directly to comment
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('comment/' . $this->id);
    }

    /**
     * Gets top comment
     *
     * @return string
     */
    public function getTopCommentAttribute()
    {
        if(!$this->parent) { return $this; }
        else {return $this->parent->topComment;}
    }

}
