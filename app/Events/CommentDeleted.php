<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Queue\SerializesModels;

class CommentDeleted
{
    use SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
}
