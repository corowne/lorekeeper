<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Models\User\User;
use App\Models\User\UserCurrency;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;

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
        $this->user = User::where('name', $name)->first();
        if(!$this->user) abort(404);
        return view('user.profile', [
            'user' => $this->user
        ]);
    }
    
    /**
     * Show a user's characters.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCharacters($name)
    {
        $this->user = User::where('name', $name)->first();
        if(!$this->user) abort(404);
        return view('user.characters', [
            'user' => $this->user
        ]);
    }
    
    /**
     * Show a user's inventory.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserInventory($name)
    {
        $this->user = User::where('name', $name)->first();
        if(!$this->user) abort(404);
        return view('user.inventory', [
            'user' => $this->user
        ]);
    }

    
    /**
     * Show a user's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserBank($name)
    {
        $this->user = User::where('name', $name)->first();
        if(!$this->user) abort(404);

        $user = $this->user;
        return view('user.bank', [
            'user' => $this->user,
            'logs' => $this->user->getCurrencyLogs(),
        ] + (Auth::check() && Auth::user()->id == $this->user->id ? [
            'currencyOptions' => Currency::where('allow_user_to_user', 1)->where('is_user_owned', 1)->whereIn('id', UserCurrency::where('user_id', $this->user->id)->pluck('currency_id')->toArray())->orderBy('sort_user', 'DESC')->pluck('name', 'id')->toArray(),
            'userOptions' => User::where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray()

        ] : []));
    }

    
    /**
     * Show a user's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCurrencyLogs($name)
    {
        $this->user = User::where('name', $name)->first();
        if(!$this->user) abort(404);

        $user = $this->user;
        return view('user.currency_logs', [
            'user' => $this->user,
            'logs' => $this->user->getCurrencyLogs(0)
        ]);
    }

}
