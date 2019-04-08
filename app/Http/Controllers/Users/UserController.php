<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use App\Models\User\User;

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
    public function getUser($name)
    {
        return view('user.profile', [
            'user' => User::where('name', $name)->first()
        ]);
    }

}
