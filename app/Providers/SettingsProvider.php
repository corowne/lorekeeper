<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SettingsProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register() {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot() {
        //
        App::bind('settings', function () {
            return new \App\Helpers\Settings;
        });
    }
}
