<?php

namespace App\Http\Controllers\Admin\Users;

use DB;
use Auth;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\User\User;
use App\Models\Rank\Rank;
use App\Models\User\UserUpdateLog;

use App\Services\UserService;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Show the user index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = User::join('ranks','users.rank_id', '=', 'ranks.id')->select('ranks.name AS rank_name', 'users.*');
        $sort = $request->only(['sort']);

        if($request->get('name')) $query->where(function($query) use ($request) {
            $query->where('users.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('users.alias', 'LIKE', '%' . $request->get('name') . '%');
        });
        if($request->get('rank_id')) $query->where('rank_id', $request->get('rank_id'));

        switch(isset($sort['sort']) ? $sort['sort'] : null) {
            default:
                $query->orderBy('ranks.sort', 'DESC')->orderBy('name');
                break;
            case 'alpha':
                $query->orderBy('name');
                break;
            case 'alpha-reverse':
                $query->orderBy('name', 'DESC');
                break;
            case 'alias':
                $query->orderBy('alias', 'ASC');
                break;
            case 'alias-reverse':
                $query->orderBy('alias', 'DESC');
                break;
            case 'rank':
                $query->orderBy('ranks.sort', 'DESC')->orderBy('name');
                break;
            case 'newest':
                $query->orderBy('created_at', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'ASC');
                break;
        }

        return view('admin.users.index', [
            'users' => $query->paginate(30)->appends($request->query()),
            'ranks' => [0 => 'Any Rank'] + Rank::orderBy('ranks.sort', 'DESC')->pluck('name', 'id')->toArray(),
            'count' => $query->count()
        ]);
    }

    /**
     * Show a user's admin page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUser($name)
    {
        $user = User::where('name', $name)->first();

        if(!$user) abort(404);

        return view('admin.users.user', [
            'user' => $user,
            'ranks' => Rank::orderBy('ranks.sort')->pluck('name', 'id')->toArray()
        ]);
    }

    public function postUserBasicInfo(Request $request, $name)
    {
        $user = User::where('name', $name)->first();
        if(!$user){
            flash('Invalid user.')->error();
        }
        elseif (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        }
        else {
            $request->validate([
                'name' => 'required|between:3,25'
            ]);
            $data = $request->only(['name'] + (!$user->isAdmin ? [1 => 'rank_id'] : []));

            $logData = ['old_name' => $user->name] + $data;
            if($user->update($data)) {
                UserUpdateLog::create(['staff_id' => Auth::user()->id, 'user_id' => $user->id, 'data' => json_encode($logData), 'type' => 'Name/Rank Change']);
                flash('Updated user\'s information successfully.')->success();
            }
            else {
                flash('Failed to update user\'s information.')->error();
            }
        }
        return redirect()->to($user->adminUrl);
    }
    
    public function postUserAlias(Request $request, $name)
    {
        $user = User::where('name', $name)->first();
        
        $logData = ['old_alias' => $user ? $user->alias : null];
        if(!$user) {
            flash('Invalid user.')->error();
        }
        else if (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        }
        else if($user->alias && $user->update(['alias' => null])) {
            UserUpdateLog::create(['staff_id' => Auth::user()->id, 'user_id' => $user->id, 'data' => json_encode($logData), 'type' => 'Clear Alias']);
            flash('Cleared user\'s alias successfully.')->success();
        }
        else {
            flash('Failed to clear user\'s alias.')->error();
        }
        return redirect()->back();
    }

    
    public function postUserAccount(Request $request, $name)
    {
        $user = User::where('name', $name)->first();
        
        if(!$user) {
            flash('Invalid user.')->error();
        }
        else if (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        }
        else if($user->settings->update(['is_fto' => $request->get('is_fto') ?: 0])) {
            UserUpdateLog::create(['staff_id' => Auth::user()->id, 'user_id' => $user->id, 'data' => json_encode(['is_fto' => $request->get('is_fto') ? 'Yes' : 'No']), 'type' => 'FTO Status Change']);
            flash('Updated user\'s account information successfully.')->success();
        }
        else {
            flash('Failed to update user\'s account information.')->error();
        }
        return redirect()->back();
    }
    
    /**
     * Show a user's account update log.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserUpdates($name)
    {
        $user = User::where('name', $name)->first();

        if(!$user) abort(404);

        return view('admin.users.user_update_log', [
            'user' => $user,
            'logs' => UserUpdateLog::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(50)
        ]);
    }
    
    /**
     * Show a user's ban page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBan($name)
    {
        $user = User::where('name', $name)->first();

        if(!$user) abort(404);

        return view('admin.users.user_ban', [
            'user' => $user
        ]);
    }
    
    /**
     * Show a user's ban confirmation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBanConfirmation($name)
    {
        $user = User::where('name', $name)->first();

        if(!$user) abort(404);

        return view('admin.users._user_ban_confirmation', [
            'user' => $user
        ]);
    }
    
    public function postBan(Request $request, UserService $service, $name)
    {
        $user = User::where('name', $name)->with('settings')->first();
        $wasBanned = $user->is_banned;
        if(!$user) {
            flash('Invalid user.')->error();
        }
        else if (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        }
        else if($service->ban(['ban_reason' => $request->get('ban_reason')], $user, Auth::user())) {
            flash($wasBanned ? 'User ban reason edited successfully.' : 'User banned successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Show a user's unban confirmation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUnbanConfirmation($name)
    {
        $user = User::where('name', $name)->with('settings')->first();

        if(!$user) abort(404);

        return view('admin.users._user_unban_confirmation', [
            'user' => $user
        ]);
    }
    
    public function postUnban(Request $request, UserService $service, $name)
    {
        $user = User::where('name', $name)->first();
        
        if(!$user) {
            flash('Invalid user.')->error();
        }
        else if (!Auth::user()->canEditRank($user->rank)) {
            flash('You cannot edit the information of a user that has a higher rank than yourself.')->error();
        }
        else if($service->unban($user, Auth::user())) {
            flash('User unbanned successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
