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
     * Show a user's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUser($name)
    {
        return view('user.profile', [
            'user' => User::where('name', $name)->first()
        ]);
    }
    
    /**
     * Show a user's characters.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCharacters($name)
    {
        return view('user.characters', [
            'user' => User::where('name', $name)->first()
        ]);
    }
    
    /**
     * Show a user's inventory.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserInventory($name)
    {
        return view('user.inventory', [
            'user' => User::where('name', $name)->first()
        ]);
    }

    
    /**
     * Show a user's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserBank($name)
    {
        return view('user.bank', [
            'user' => User::where('name', $name)->first()
        ]);
    }

}
