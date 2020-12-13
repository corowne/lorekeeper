<?php

namespace App\Http\Controllers\Users;

use Auth;
use File;
use Image;

use App\Models\User\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;

use App\Services\UserService;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Account Controller
    |--------------------------------------------------------------------------
    |
    | Handles the user's account management.
    |
    */

    /**
     * Shows the banned page, or redirects the user to the home page if they aren't banned.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function getBanned()
    {
        if(Auth::user()->is_banned)
            return view('account.banned');
        else 
            return redirect()->to('/');
    }

    /**
     * Shows the user settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSettings()
    {
        return view('account.settings');
    }
    
    /**
     * Edits the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postProfile(Request $request)
    {
        Auth::user()->profile->update([
            'text' => $request->get('text'),
            'parsed_text' => parse($request->get('text'))
        ]);
        flash('Profile updated successfully.')->success();
        return redirect()->back();
    }

    /**
     * Edits the user's avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAvatar(Request $request, UserService $service)
    {
        if($service->updateAvatar($request->file('avatar'), Auth::user())) {
            flash('Avatar updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Changes the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPassword(Request $request, UserService $service)
    {
        $request->validate( [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);
        if($service->updatePassword($request->only(['old_password', 'new_password', 'new_password_confirmation']), Auth::user())) {
            flash('Password updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Changes the user's email address and sends a verification email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEmail(Request $request, UserService $service)
    {
        $request->validate( [
            'email' => 'required|string|email|max:255|unique:users'
        ]);
        if($service->updateEmail($request->only(['email']), Auth::user())) {
            flash('Email updated successfully. A verification email has been sent to your new email address.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the notifications page.
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
    
    /**
     * Deletes a notification and returns a response.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDeleteNotification($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if($notification) $notification->delete();
        return response(200);
    }

    /**
     * Deletes all of the user's notifications.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postClearNotifications($type = null)
    {
        if(isset($type) && $type) Auth::user()->notifications()->where('notification_type_id', $type)->delete();
        else Auth::user()->notifications()->delete();
        flash('Notifications cleared successfully.')->success();
        return redirect()->back();
    }
}
