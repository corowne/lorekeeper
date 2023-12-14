<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Route;

class FortifyServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() {
        $this->configureRoutes();

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        $altRegistrations = array_filter(config('lorekeeper.sites'), function ($item) {
            return isset($item['login']) && $item['login'] === 1 && $item['display_name'] != 'tumblr';
        });
        Fortify::registerView(fn () => view('auth.register', [
            'userCount'        => User::count(),
            'altRegistrations' => $altRegistrations,
        ]));

        $altLogins = array_filter(config('lorekeeper.sites'), function ($item) {
            return isset($item['login']) && $item['login'] === 1 && $item['display_name'] != 'tumblr';
        });
        Fortify::loginView(fn () => view('auth.login', [
            'userCount' => User::count(),
            'altLogins' => $altLogins,
        ]));

        Fortify::requestPasswordResetLinkView(fn () => view('auth.passwords.forgot'));
        Fortify::resetPasswordView(fn () => view('auth.passwords.reset'));
        Fortify::verifyEmailView(fn () => view('auth.verify'));
        Fortify::confirmPasswordView(fn () => view('auth.passwords.confirm'));

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });
    }

    /**
     * Configure the routes offered by the application.
     */
    protected function configureRoutes() {
        if (Fortify::$registersRoutes) {
            Route::group([
                'namespace' => 'Laravel\Fortify\Http\Controllers',
                'domain'    => config('fortify.domain', null),
                'prefix'    => config('fortify.path'),
            ], function () {
                $this->loadRoutesFrom(base_path('routes/fortify.php'));
            });
        }
    }
}
