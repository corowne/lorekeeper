<?php

namespace App\Helpers;

use DB;

class Settings {
    public function get($key)
    {
        $setting = DB::table('site_settings')->where('key', $key)->first();
        if($setting) return $setting->value;
        else return null;
    }
}