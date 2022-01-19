<?php

namespace App\Traits;

use App\Models\Comment;
use Config;

/**
 * Add this trait to any model that you want to be able to
 * comment upon or get comments for.
 */
trait Commentable
{
    /**
     * Returns all comments for this model.
     */
    public function commentz()
    {
        return $this->morphMany('App\Models\Comment', 'commentable')->withTrashed();
    }

    /**
     * Returns only approved comments for this model.
     */
    public function approvedComments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable')->where('approved', true)->withTrashed();
    }

    /**
     * This static method does voodoo magic to
     * delete leftover comments once the commentable
     * model is deleted.
     */
    protected static function bootCommentable()
    {
        static::deleted(function ($commentable) {
            if (Config::get('lorekeeper.comments.soft_deletes') == true) {
                Comment::where('commentable_type', get_class($commentable))->where('commentable_id', $commentable->id)->delete();
            } else {
                Comment::where('commentable_type', get_class($commentable))->where('commentable_id', $commentable->id)->forceDelete();
            }
        });
    }
}
