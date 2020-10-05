<?php

namespace App\Http\Controllers;

use Auth;
use DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\Model;

use App\Models\Comment;

class PermalinkController extends Controller
{
     /**
     * returns replies recursively
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     public function getComment($id) {

        $comments = Comment::all();
        //$comments = $comments->sortByDesc('created_at');
        $comment = $comments->find($id);

        if($comment->commentable_type == 'App\Models\User\UserProfile') $comment->location = $comment->commentable->user->url;
        else $comment->location = $comment->commentable->url;
        
        return view('comments._perma_layout',[
            'comment' => $comment,            
        ]);
    }

     /**
     * returns replies recursively
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     public function replies() {
        return ' what ';
    }
}