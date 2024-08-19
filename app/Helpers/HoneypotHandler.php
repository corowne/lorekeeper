<?php

namespace App\Helpers;

use Closure;
use Illuminate\Http\Request;
use Spatie\Honeypot\SpamResponder\SpamResponder;

class HoneypotHandler implements SpamResponder {
    public function respond(Request $request, Closure $next) {
        flash('Woah there! Your request has activated anti-spam measures. If you believe this to be in error, please wait a few seconds before submitting the form again.')->warning();

        return redirect()->back();
    }
}
