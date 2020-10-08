<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class CommentProvider extends ServiceProvider
{

    /**
     * If for some reason you want to override the component.
     */
    protected function includeBladeComponent()
    {
        Blade::include('comments::comments', 'comments');
    }

    /**
     * Define permission defined in the config.
     */
    protected function definePermissions()
    {
        foreach(Config::get('lorekeeper.comments.permissions', []) as $permission => $policy) {
            Gate::define($permission, $policy);
        }
    }

    public function boot()
    {
        $this->loadViewsFrom(base_path('resources/views/comments'), 'comments');

        $this->includeBladeComponent();

        $this->definePermissions();

        Route::model('comment', 'App\Models\Comment');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            base_path('config/lorekeeper/comments.php'),
            'comments'
        ); // Swapped to / instead of \ because for some reason Dreamhost was Not Happy if it was \
    }
}
