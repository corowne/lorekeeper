<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

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

}
