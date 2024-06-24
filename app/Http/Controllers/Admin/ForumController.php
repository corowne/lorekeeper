<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;

use App\Models\Forum;
use App\Models\Rank\Rank;

use App\Services\ForumService;

use App\Http\Controllers\Controller;

class ForumController extends Controller
{
    /**
     * Shows the forum index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.forums.forums', [
            'forums' => Forum::orderBy('sort')->paginate(20)
        ]);
    }

    /**
     * Shows the create forum forum.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateForum()
    {
        return view('admin.forums.create_edit_forum', [
            'forum' => new Forum,
            'forums' => Forum::visible()->orderBy('sort', 'DESC')->pluck('name','id')->toArray(),
            'ranks' => Rank::orderBy('sort', 'DESC')->pluck('name','id')->toArray()

        ]);
    }

    /**
     * Shows the edit forum forum.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditForum($id)
    {
        $forum = Forum::find($id);
        if(!$forum) abort(404);
        return view('admin.forums.create_edit_forum', [
            'forum' => $forum,
            'forums' => Forum::visible()->orderBy('sort', 'DESC')->pluck('name','id')->toArray(),
            'ranks' => Rank::orderBy('sort', 'DESC')->pluck('name','id')->toArray()
        ]);
    }

    /**
     * Creates or edits a forum.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\ForumService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditForum(Request $request, ForumService $service, $id = null)
    {
        $request->validate(['image' => 'nullable|mimes:png,jpg,jpeg,gif|max:20000']);
        $data = $request->only([
            'name', 'description', 'layout', 'is_active', 'is_locked', 'staff_only', 'sort', 'role_limit', 'parent_id', 'image', 'remove_image'
        ]);
        if($id && $service->updateForum(Forum::find($id), $data, Auth::user())) {
            flash('Forum updated successfully.')->success();
        }
        else if (!$id && $forum = $service->createForum($data, Auth::user())) {
            flash('Forum created successfully.')->success();
            return redirect()->to('admin/forums/edit/'.$forum->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the forum deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteForum($id)
    {
        $forum = Forum::find($id);
        return view('admin.forums._delete_forum', [
            'forum' => $forum,
        ]);
    }

    /**
     * Deletes a forum.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\ForumService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteForum(Request $request, ForumService $service, $id)
    {
        $data = $request->only(['child_boards']);
        if($id && $service->deleteForum(Forum::find($id), $data)) {
            flash('Forum deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/forums');
    }
}
