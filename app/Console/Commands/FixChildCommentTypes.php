<?php

namespace App\Console\Commands;

use App\Models\Comment;

use Illuminate\Console\Command;
class FixChildCommentTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-child-comment-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes types of child comments that don\'t match their parent.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $childComments = Comment::whereNotNull('child_id')->get();
        if($childComments->count()) {
            $this->line('Updating '.$childComments->count().' comments...');
            foreach($childComments as $comment) {
                $parent = Comment::find($comment->child_id);
                if(isset($parent->type) && $comment->type != $parent->type)
                    $comment->update(['type' => $parent->type]);
            }
            $this->line('Complete!');
        }
        else $this->line('No comments to update!');
    }
}
