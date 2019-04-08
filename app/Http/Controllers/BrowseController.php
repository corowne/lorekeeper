<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Models\User\User;

class BrowseController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUsers()
    {
        return view('browse.users', [  
            'users' => User::paginate(50)
        ]);
    }
}
