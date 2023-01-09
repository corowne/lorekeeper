<?php

namespace App\Policies;

use App\Models\Comment;
use Auth;

class CommentPolicy {
    /**
     * Can user create the comment.
     */
    public function create($user): bool {
        return true;
    }

    /**
     * Can user delete the comment.
     */
    public function delete($user, Comment $comment): bool {
        if (auth::user()->isStaff) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Can user update the comment.
     */
    public function update($user, Comment $comment): bool {
        return $user->getKey() == $comment->commenter_id;
    }

    /**
     * Can user reply to the comment.
     */
    public function reply($user, Comment $comment): bool {
        return $user->getKey();
    }
}
