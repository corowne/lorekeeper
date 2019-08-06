<?php

namespace App\Http\Controllers\Users;

use Auth;
use Illuminate\Http\Request;

use App\Models\Notification;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the user settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSettings()
    {
        return view('account.settings', [
        ]);
    }

    /**
     * Show the notifications page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNotifications()
    {
        $notifications = Auth::user()->notifications()->orderBy('id', 'DESC')->paginate(30);
        Auth::user()->notifications()->update(['is_unread' => 0]);
        Auth::user()->notifications_unread = 0;
        Auth::user()->save();

        return view('account.notifications', [
            'notifications' => $notifications
        ]);
    }
    
    public function getDeleteNotification($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if($notification) $notification->delete();
        return response(200);
    }

    public function postClearNotifications()
    {
        Auth::user()->notifications()->delete();
        flash('Notifications cleared successfully.')->success();
        return redirect()->back();
    }
}
