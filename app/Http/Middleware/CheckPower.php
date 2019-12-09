<?php

namespace App\Http\Middleware;

use Closure;

class CheckPower
{
    /**
     * Check if the user has the power to access the current page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $power)
    {
        if (!$request->user()->hasPower($power)) {
            flash('You do not have the permission to access this page.')->error();
            return redirect('/');
        }

        return $next($request);
    }
}
