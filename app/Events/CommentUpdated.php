<?php

namespace App\Events;

use App\Models\Comment\Comment;
use Illuminate\Queue\SerializesModels;

class CommentUpdated {
    use SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }
}
