<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;

use App\Models\User\User;
use App\Models\Rank\Rank;

use App\Http\Controllers\Controller;

class UserController extends Controller
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
     * Show the user index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = User::join('ranks','users.rank_id', '=', 'ranks.id')->select('ranks.name AS rank_name', 'users.*');

        if($request->get('name')) $query->where(function($query) use ($request) {
            $query->where('users.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('users.alias', 'LIKE', '%' . $request->get('name') . '%');
        });
        if($request->get('rank_id')) $query->where('rank_id', $request->get('rank_id'));

        return view('admin.users.index', [
            'users' => $query->orderBy('ranks.sort', 'DESC')->paginate(30),
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
}
