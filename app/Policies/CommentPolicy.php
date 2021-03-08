<?php

namespace App\Policies;

use Auth;
use App\Models\Comment;

class CommentPolicy
{
    /**
     * Can user create the comment
     *
     * @param $user
     * @return bool
     */
    public function create($user) : bool
    {
        return true;
    }

    /**
     * Can user delete the comment
     *
     * @param $user
     * @param Comment $comment
     * @return bool
     */
    public function delete($user, Comment $comment) : bool
    {
            if(auth::user()->isStaff) {
                return true;
            }
            else {
                return false;
            }
    }

    /**
     * Can user update the comment
     *
     * @param $user
     * @param Comment $comment
     * @return bool
     */
    public function update($user, Comment $comment) : bool
    {
        if($comment->topComment->is_locked || $comment->commentable_type == 'App\Models\Forum' && $comment->commentable->canUsersPost()) {
            if($user->isStaff) return $user->getKey() == $comment->commenter_id;
            else return false;
        }
        else return $user->getKey() == $comment->commenter_id;
    }

    /**
     * Can user reply to the comment
     *
     * @param $user
     * @param Comment $comment
     * @return bool
     */
    public function reply($user, Comment $comment) : bool
    {
        if($comment->topComment->is_locked || $comment->commentable_type == 'App\Models\Forum' && !$comment->commentable->canUsersPost())
        {
            if($user->isStaff) return $user->getKey() == $user->getKey();
            else return false;
        }
        else return $user->getKey();
    }
}

