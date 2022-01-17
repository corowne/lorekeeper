<?php

namespace App\Http\Middleware;

use Closure;
use Settings;

class CheckAlias
{
    /**
     * Redirects users without an alias to the dA account linking page,
     * and banned users to the ban page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Settings::get('is_maintenance_mode') == 1 && !$request->user()->hasPower('maintenance_access')) {
            return redirect('/');
        }
        if(!$request->user()->has_alias) {
            return redirect('/link');
        }
        if(!$request->user()->birthday) {
            return redirect('/birthday');
        }
        if(!$request->user()->checkBirthday) {
            return redirect('/blocked');
        }
        if($request->user()->is_banned) {
            return redirect('/banned');
        }

        return $next($request);
    }
}
