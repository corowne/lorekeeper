<?php

namespace App\Http\Controllers\Users;

use Auth;

use Illuminate\Http\Request;

use App\Services\BookmarkManager;
use App\Models\Character\CharacterBookmark;

use App\Http\Controllers\Controller;

class BookmarkController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Bookmark Controller
    |--------------------------------------------------------------------------
    |
    | Handles the user's character bookmarks.
    |
    */

    /**
     * Shows the bookmarks page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBookmarks()
    {
        return view('account.bookmarks', [
            'bookmarks' => CharacterBookmark::with('character.image')->with('character.user')->visible()->where('character_bookmarks.user_id', Auth::user()->id)->paginate(20)
        ]);
    }
    
    /**
     * Gets the bookmark creation modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateBookmark()
    {
        return view('account.bookmarks._create_edit_bookmark', [
            'bookmark' => new CharacterBookmark,
        ]);
    }
    
    /**
     * Gets the bookmark editing modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditBookmark($id)
    {
        $bookmark = CharacterBookmark::with('character')->where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!$bookmark) abort(404);
        return view('account.bookmarks._create_edit_bookmark', [
            'bookmark' => $bookmark,
        ]);
    }

    /**
     * Creates or edits a bookmark.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  App\Services\BookmarkManager  $service
     * @param  int|null                      $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditBookmark(Request $request, BookmarkManager $service, $id = null)
    {
        $id ? $request->validate(CharacterBookmark::$updateRules) : $request->validate(CharacterBookmark::$createRules);
        $data = $request->only([
            'character_id', 'notify_on_trade_status', 'notify_on_gift_art_status', 'notify_on_gift_writing_status', 'notify_on_transfer', 'notify_on_image', 'comment'
        ]);
        if($id && $service->updateBookmark($data + ['bookmark_id' => $id], Auth::user())) {
            flash('Bookmark updated successfully.')->success();
        }
        else if (!$id && $bookmark = $service->createBookmark($data, Auth::user())) {
            flash('Bookmark created successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the bookmark deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteBookmark($id)
    {
        $bookmark = CharacterBookmark::with('character')->where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!$bookmark) abort(404);
        return view('account.bookmarks._delete_bookmark', [
            'bookmark' => $bookmark,
        ]);
    }

    /**
     * Deletes a bookmark.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  App\Services\BookmarkManager  $service
     * @param  int                           $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteBookmark(Request $request, BookmarkManager $service, $id)
    {
        if($id && $service->deleteBookmark(['bookmark_id' => $id], Auth::user())) {
            flash('Bookmark deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
