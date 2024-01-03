<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class Settings {
    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Retrieves site settings as defined in the database.
    |
    */

    /**
     * Gets a site setting.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key) {
        $setting = DB::table('site_settings')->where('key', $key)->first();
        if ($setting) {
            return $setting->value;
        } else {
            return null;
        }
    }
}
