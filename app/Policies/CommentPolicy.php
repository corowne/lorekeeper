<?php

namespace App\Policies;

use App\Models\Comment\Comment;
use Config;
use Illuminate\Support\Facades\Auth;

class CommentPolicy {
    /**
     * Can user create the comment.
     *
     * @param mixed $user
     */
    public function create($user): bool {
        return true;
    }

    /**
     * Can user delete the comment.
     *
     * @param mixed $user
     */
    public function delete($user, Comment $comment): bool {
        if (Auth::user()->isStaff) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Can user update the comment.
     *
     * @param mixed $user
     */
    public function update($user, Comment $comment): bool {
        $canEdit = Config::Get('lorekeeper.extensions.forum_author_edit');
        if ($comment->topComment->is_locked || $comment->commentable_type == 'App\Models\Forum' && $comment->commentable->canUsersPost()) {
            if ($user->isStaff) {
                return $user->getKey() == $comment->commenter_id;
            } elseif ($comment->commentable_type == 'App\Models\Forum' && $canEdit) {
                return $user->getKey() == $comment->commenter_id;
            } else {
                return false;
            }
        } else {
            return $user->getKey() == $comment->commenter_id;
        }
    }

    /**
     * Can user reply to the comment.
     *
     * @param mixed $user
     */
    public function reply($user, Comment $comment): bool {
        if ($comment->topComment->is_locked || $comment->commentable_type == 'App\Models\Forum' && !$comment->commentable->canUsersPost()) {
            if ($user->isStaff) {
                return $user->getKey() == $user->getKey();
            } else {
                return false;
            }
        } else {
            return $user->getKey();
        }
    }
}
