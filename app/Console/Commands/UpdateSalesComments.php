<?php

namespace App\Console\Commands;

use App\Models\Comment;

use Illuminate\Console\Command;
class UpdateSalesComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-sales-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates existing comments on sales posts to use new model namespace.';

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
        $salesComments = Comment::where('commentable_type', 'App\Models\Sales')->get();
        if($salesComments->count()) {
            $this->line('Updating '.$salesComments->count().' comments...');
            foreach($salesComments as $comment) {
                $comment->commentable_type = 'App\Models\Sales\Sales';
                $comment->save();
            }
            $this->line('Complete!');
        }
        else $this->line('No comments to update!');
    }
}
