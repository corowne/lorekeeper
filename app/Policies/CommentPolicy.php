<?php

namespace App\Policies;

use App\Models\Comment;
use Auth;

class CommentPolicy
{
    /**
     * Can user create the comment.
     *
     * @param $user
     */
    public function create($user) : bool
    {
        return true;
    }

    /**
     * Can user delete the comment.
     *
     * @param $user
     */
    public function delete($user, Comment $comment) : bool
    {
        if (auth::user()->isStaff) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Can user update the comment.
     *
     * @param $user
     */
    public function update($user, Comment $comment) : bool
    {
        return $user->getKey() == $comment->commenter_id;
    }

    /**
     * Can user reply to the comment.
     *
     * @param $user
     */
    public function reply($user, Comment $comment) : bool
    {
        return $user->getKey();
    }
}
