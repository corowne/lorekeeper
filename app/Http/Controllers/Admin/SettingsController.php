<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller {
    /**
     * Shows the settings index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.settings.settings', [
            'settings' => DB::table('site_settings')->orderBy('key')->paginate(20),
        ]);
    }

    /**
     * Edits a setting.
     *
     * @param string $key
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditSetting(Request $request, $key) {
        if (DB::table('site_settings')->where('key', $key)->update(['value' => $request->get('value')])) {
            flash('Setting updated successfully.')->success();
        } else {
            flash('Invalid setting selected.')->success();
        }

        return redirect()->back();
    }
}
