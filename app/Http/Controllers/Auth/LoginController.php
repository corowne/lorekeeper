<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\User\UserAlias;
use App\Services\LinkService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm() {
        $altLogins = array_filter(Config::get('lorekeeper.sites'), function ($item) {
            return isset($item['login']) && $item['login'] === 1 && $item['display_name'] != 'tumblr';
        });

        return view('auth.login', ['userCount' => User::count(), 'altLogins' => $altLogins]);
    }

    /**
     * Authenticate via Aliases.
     *
     * @param mixed $provider
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuthRedirect(LinkService $service, $provider) {
        $result = $service->getAuthRedirect($provider, true);

        return $result;
    }

    /**
     * Authenticate via Aliases.
     *
     * @param mixed $provider
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuthCallback(LinkService $service, $provider) {
        // Toyhouse runs on Laravel Passport for OAuth2 and this has some issues with state exceptions,
        // admin suggested the easy fix (to use stateless)
        $socialite = $provider == 'toyhouse' ? Socialite::driver($provider)->stateless() : Socialite::driver($provider);
        // Needs to match for the user call to work
        $socialite->redirectUrl(str_replace('auth', 'login', url(Config::get('services.'.$provider.'.redirect'))));
        $result = $socialite->user();

        $user = UserAlias::where('user_snowflake', $result->id)->first();
        if (!$user) {
            return redirect('/register/'.$provider)->with(['userData' => $result]);
        }

        Auth::login($user->user);

        return redirect($this->redirectTo);
    }
}
