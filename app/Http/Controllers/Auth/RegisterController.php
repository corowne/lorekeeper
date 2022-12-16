<?php

namespace App\Http\Controllers\Auth;

use DB;
use Settings;
use Carbon\Carbon;

use App\Models\User\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Arr;

use App\Models\Invitation;
use App\Models\User\UserAlias;
use App\Services\UserService;
use App\Services\InvitationService;
use App\Services\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegisterWithDriver($provider) {
        $userData = session()->get('userData');

        return view('auth.register_with_driver', [
            'userCount' => User::count(),
            'provider' => $provider,
            'user' => $userData->nickname ?? null,
            'token' => $userData->token ?? null,
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegisterWithDriver(LinkService $service, Request $request, $provider) {
        $providerData = Socialite::driver('toyhouse')->userFromToken($request->get('token'));

        if (UserAlias::where('site', $provider)->where('user_snowflake', $providerData->id)->first()) {
            flash("An Account is already tied to the authorized " . $provider . " account.")->error();
            return redirect()->back();
        }

        $data = $request->all();

        $this->validator($data, true)->validate();
        $user = $this->create($data);
        if ($service->saveProvider($provider, $providerData, $user)) {
            Auth::login($user);
            return redirect('/');
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) flash($error)->error();
            return redirect()->back();
        }
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm() {
        return view('auth.register', ['userCount' => User::count()]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $socialite = false) {
        return Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:25', 'alpha_dash', 'unique:users'],
            'email' => ($socialite ? [] : ['required']) + ['string', 'email', 'max:255', 'unique:users'],
            'agreement' => ['required', 'accepted'],
            'password' => ($socialite ? [] : ['required']) + ['string', 'min:8', 'confirmed'],
            'dob' => [
                'required', function ($attribute, $value, $fail) { {
                        $date = $value['day'] . "-" . $value['month'] . "-" . $value['year'];
                        $formatDate = carbon::parse($date);
                        $now = Carbon::now();
                        if ($formatDate->diffInYears($now) < 13) {
                            $fail('You must be 13 or older to access this site.');
                        }
                    }
                }
            ],
            'code' => [
                'string', function ($attribute, $value, $fail) {
                    if (!Settings::get('is_registration_open')) {
                        if (!$value) $fail('An invitation code is required to register an account.');
                        $invitation = Invitation::where('code', $value)->whereNull('recipient_id')->first();
                        if (!$invitation) $fail('Invalid code entered.');
                    }
                }
            ]
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User\User
     */
    protected function create(array $data) {
        DB::beginTransaction();
        $service = new UserService;
        $user = $service->createUser(Arr::only($data, ['name', 'email', 'password', 'dob']));
        if (!Settings::get('is_registration_open')) {
            (new InvitationService)->useInvitation(Invitation::where('code', $data['code'])->first(), $user);
        }
        DB::commit();
        return $user;
    }
}
