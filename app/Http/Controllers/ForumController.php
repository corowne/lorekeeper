<?php

namespace App\Http\Controllers;

use Settings;
use Config;
use Auth;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\User\User;
use App\Models\Comment;
use App\Models\Forum;

use App\Services\ForumService;

class ForumController extends Controller
{
    /**
     * Shows the forums index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        $forums = Forum::with('children')->has('children')->visible()->category()->orderBy('sort', 'DESC')->staff()->get();
        $customforums = collect();

        foreach($forums as $key => $forum)
        {
            foreach($forum->children as $child)
            {
                if($forum->hasRestrictions && !Auth::check()) break;
                elseif(!$child->hasRestrictions || Auth::check() && Auth::user()->canVisitForum($forum->id)) {
                    $customforums->push($forum);
                    break;
                }
            }
        }

        return view('forums.index', [
            'forums' => $customforums
        ]);
    }


    /**
     * Shows an individual board's page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getForum($id)
    {
        $board = Forum::where('id',$id)->visible()->first();
        if(!$board) abort(404);

        if($board->hasRestrictions && (!Auth::check() || Auth::check() && !Auth::user()->canVisitForum($id))) {
            flash('You do not have permission to access this forum.')->error();
            return redirect(url('/'));
        }
        elseif($board->parent ? (($board->parent->hasRestrictions && !Auth::check()) || Auth::check() && !Auth::user()->canVisitForum($board->parent->id)) : false) {
            flash('You do not have permission to access this forum.')->error();
            return redirect(url('/'));
        }
        elseif($board->parent && $board->parent->parent ? (($board->parent->parent->hasRestrictions && !Auth::check()) || Auth::check() && !Auth::user()->canVisitForum($board->parent->parent->id)) : false) {
            flash('You do not have permission to access this forum.')->error();
            return redirect(url('/'));
        }

        return view('forums.forum', [
            'forum' => $board,
            'posts' => $board->comments->whereNull('child_id')->sortByDesc('latestReplyTime')->sortByDesc('is_featured')->paginate(20)
        ]);
    }

    /**
     * Shows an individual board's page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getThread($board_id,$id)
    {
        $thread = Comment::where('id',$id)->where('commentable_type','App\Models\Forum')->first();
        if(!$thread) abort(404);

        if($thread->commentable->hasRestrictions && (!Auth::check() || Auth::check() && !Auth::user()->canVisitForum($board_id))) {
            flash('You do not have permission to access this thread.')->error();
            return redirect(url('/'));
        }
        elseif($thread->commentable->parent ? (($thread->commentable->parent->hasRestriction && !Auth::check()) || Auth::check() && !Auth::user()->canVisitForum($thread->commentable->parent->id)) : false) {
            flash('You do not have permission to access this thread.')->error();
            return redirect(url('/'));
        }
        elseif($thread->commentable->parent && $thread->commentable->parent->parent ? (($thread->commentable->parent->parent->hasRestriction && !Auth::check()) || Auth::check() && !Auth::user()->canVisitForum($thread->commentable->parent->parent->id)) : false) {
            flash('You do not have permission to access this thread.')->error();
            return redirect(url('/'));
        }

        return view('forums.thread', [
            'thread' => $thread,
            'replies' => $thread->getAllChildren()->sortBy('created_at')->paginate(15)
        ]);
    }

    /**
     * Shows the create forum forum.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateThread($id)
    {
        if(!Auth::user()->canVisitForum($id)) abort(404);
        $forum = Forum::find($id);
        return view('forums.create_thread', [
            'forum' => $forum,
            'thread' => new Comment

        ]);
    }

    /**
     * Shows the create forum forum.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditThread($id, $thread_id)
    {

        $thread = Comment::find(intval($thread_id));
        if(!$thread) abort(404);

        $forum = Forum::find($id);
        if(!$forum) abort(404);
        if($thread->commentable_id != $id) abort(404);

        if(!Gate::check('edit-comment', [$thread])) {
            flash('You do not have permission to edit this thread.')->error();
            return redirect()->back();
        }
        return view('forums.edit_thread', [
            'forum' => $forum,
            'thread' => $thread

        ]);
    }




}
