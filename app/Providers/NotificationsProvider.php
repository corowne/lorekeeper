<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class NotificationsProvider extends ServiceProvider {
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
        App::bind('notifications', function () {
            return new \App\Helpers\Notifications;
        });
    }
}
