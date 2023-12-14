<?php

namespace App\Models\Comment;

use App\Models\Model;
use App\Models\User\User;

class CommentLike extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'comment_id', 'is_like',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comment_likes';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the comment.
     */
    public function comment() {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the user who liked the comment.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
