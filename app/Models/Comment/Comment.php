<?php

namespace App\Models\Comment;

use App\Events\CommentCreated;
use App\Events\CommentDeleted;
use App\Events\CommentUpdated;
use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model {
    use SoftDeletes;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'commenter',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 'approved', 'guest_name', 'guest_email', 'is_featured', 'type', 'title', 'is_locked'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'approved' => 'boolean',
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
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

     **********************************************************************************************/

    /**
     * The user who posted the comment.
     */
    public function commenter() {
        return $this->morphTo();
    }

    /**
     * The model that was commented upon.
     */
    public function commentable() {
        return $this->morphTo()->withTrashed();
    }

    /**
     * Returns all comments that this comment is the parent of.
     */
    public function children() {
        return $this->hasMany(self::class, 'child_id')->withTrashed()->with('children');
    }

    /**
     * Returns the comment to which this comment belongs to.
     */
    public function parent() {
        return $this->belongsTo(self::class, 'child_id')->withTrashed()->with('children');
    }

    /**
     * Gets the likes for this comment.
     */
    public function likes() {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Get the edit history of the comment.
     */
    public function edits() {
        return $this->hasMany(CommentEdit::class)->orderBy('created_at', 'desc');
    }

    /**********************************************************************************************

        ATTRIBUTES

     **********************************************************************************************/


    /**
     * Gets / Creates permalink for comments - allows user to go directly to comment
     *
     * @return string
     */
    public function getThreadUrlAttribute()
    {
        return url('forum/'.$this->commentable_id . '/~' . $this->topComment->id);
    }

    /**
     * Gets and Returns a display name for the comment.
     * If this is the start of a forum topic, uses the Title attribute and leads to a forum
     */
    public function getDisplayNameAttribute()
    {
        if($this->commentable_type == 'App\Models\Forum') {
            if(isset($this->title)) return '<a href="'.$this->threadUrl.'">'.$this->title.'</a>';
            else return '<a href="'.$this->threadUrl.'">Re: '.$this->topComment->title.'</a>';
        }
        else return '<a href="'.$this->url.'">Comment</a> by '.$this->commenter->displayName;
    }

    /**
     * Gets / Creates permalink for comments - allows user to go directly to comment.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('comment/'.$this->id);
    }

    /**
     * Gets top comment.
     *
     * @return string
     */
    public function getTopCommentAttribute() {
        if (!$this->parent) {
            return $this;
        } else {
            return $this->parent->topComment;
        }
    }

    
    /**
     * Gets top comment
     *
     * @return string
     */
    public function getLatestReplyAttribute()
    {
        return Comment::where('child_id',$this->id)->latest()->first();
    }

    public function getLatestReplyTimeAttribute()
    {
        if($this->latestReply) return $this->latestReply->created_at;
        else return $this->created_at;
    }

    /**
     * Returns all children of this comment, not just direct.
     */
    public function getAllChildren()
    {
        $sections = collect();

        foreach ($this->children as $section) {
            $sections->push($section);
            $sections = $sections->merge($section->getAllChildren());
        }

        return $sections;
    }
}
