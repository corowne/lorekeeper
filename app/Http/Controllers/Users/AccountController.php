<?php

namespace App\Http\Controllers\Users;

use Auth;
use File;
use Image;

use App\Models\User\User;
use App\Models\User\UserAlias;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;

use App\Services\UserService;
use App\Services\LinkService;

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
     * Changes user birthday setting
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBirthday(Request $request, UserService $service)
    {
        if($service->updateDOB($request->input('birthday_setting'), Auth::user())) {
            flash('Setting updated successfully.')->success();
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
        if(isset($type))
            Auth::user()->notifications()->where('notification_type_id', $type)->delete();
        else Auth::user()->notifications()->delete();
        flash('Notifications cleared successfully.')->success();
        return redirect()->back();
    }

    /**
     * Shows the account links page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAliases()
    {
        return view('account.aliases');
    }

    /**
     * Shows the make primary alias modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMakePrimary($id)
    {
        return view('account._make_primary_modal', ['alias' => UserAlias::where('id', $id)->where('user_id', Auth::user()->id)->first()]);
    }

    /**
     * Makes an alias the user's primary alias.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMakePrimary(LinkService $service, $id)
    {
        if($service->makePrimary($id, Auth::user())) {
            flash('Your primary alias has been changed successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the hide alias modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getHideAlias($id)
    {
        return view('account._hide_alias_modal', ['alias' => UserAlias::where('id', $id)->where('user_id', Auth::user()->id)->first()]);
    }

    /**
     * Hides or unhides the selected alias from public view.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postHideAlias(LinkService $service, $id)
    {
        if($service->hideAlias($id, Auth::user())) {
            flash('Your alias\'s visibility setting has been changed successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the remove alias modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRemoveAlias($id)
    {
        return view('account._remove_alias_modal', ['alias' => UserAlias::where('id', $id)->where('user_id', Auth::user()->id)->first()]);
    }

    /**
     * Removes the selected alias from the user's account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRemoveAlias(LinkService $service, $id)
    {
        if($service->removeAlias($id, Auth::user())) {
            flash('Your alias has been removed successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
