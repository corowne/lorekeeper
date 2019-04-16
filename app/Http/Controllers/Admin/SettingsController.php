<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * Show the settings index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.settings.settings', [
            'settings' => DB::table('site_settings')->orderBy('key')->paginate(20)
        ]);
    }

    public function postEditSetting(Request $request, $key)
    {
        if(DB::table('site_settings')->where('key', $key)->update(['value' => $request->get('value')])) {
            flash('Setting updated successfully.')->success();
        }
        else {
            flash('Invalid setting selected.')->success();
        }
        return redirect()->back();
    }
}
