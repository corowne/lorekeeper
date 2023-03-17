<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User\User;
use App\Models\User\UserAlias;
use App\Services\InvitationService;
use App\Services\LinkService;
use App\Services\UserService;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Settings;

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
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @param mixed $provider
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegisterWithDriver($provider) {
        $userData = session()->get('userData');

        return view('auth.register_with_driver', [
            'userCount' => User::count(),
            'provider'  => $provider,
            'user'      => $userData->nickname ?? null,
            'token'     => $userData->token ?? null,
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @param mixed $provider
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegisterWithDriver(LinkService $service, Request $request, $provider) {
        $providerData = Socialite::driver($provider)->userFromToken($request->get('token'));

        if (UserAlias::where('site', $provider)->where('user_snowflake', $providerData->id)->first()) {
            flash('An Account is already tied to the authorized '.$provider.' account.')->error();

            return redirect()->back();
        }

        $data = $request->all();

        $this->validator($data, true)->validate();
        $user = $this->create($data);
        if ($service->saveProvider($provider, $providerData, $user)) {
            Auth::login($user);

            return redirect('/');
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm() {
        $altRegistrations = array_filter(Config::get('lorekeeper.sites'), function ($item) {
            return isset($item['login']) && $item['login'] === 1 && $item['display_name'] != 'tumblr';
        });

        return view('auth.register', ['userCount' => User::count(), 'altRegistrations' => $altRegistrations]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param mixed $socialite
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $socialite = false) {
        return Validator::make($data, [
            'name'      => ['required', 'string', 'min:3', 'max:25', 'alpha_dash', 'unique:users'],
            'email'     => ($socialite ? [] : ['required']) + ['string', 'email', 'max:255', 'unique:users'],
            'agreement' => ['required', 'accepted'],
            'password'  => ($socialite ? [] : ['required']) + ['string', 'min:8', 'confirmed'],
            'dob'       => [
                'required', function ($attribute, $value, $fail) {
                    $date = $value['day'].'-'.$value['month'].'-'.$value['year'];
                    $formatDate = carbon::parse($date);
                    $now = Carbon::now();
                    if ($formatDate->diffInYears($now) < 13) {
                        $fail('You must be 13 or older to access this site.');
                    }
                },
            ],
            'code'                 => ['string', function ($attribute, $value, $fail) {
                if (!Settings::get('is_registration_open')) {
                    if (!$value) {
                        $fail('An invitation code is required to register an account.');
                    }
                    $invitation = Invitation::where('code', $value)->whereNull('recipient_id')->first();
                    if (!$invitation) {
                        $fail('Invalid code entered.');
                    }
                }
            },
            ],
            'g-recaptcha-response' => 'required|recaptchav3:register,0.5',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
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
