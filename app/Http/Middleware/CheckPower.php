<?php

namespace App\Http\Middleware;

use Closure;

class CheckPower
{
    /**
     * Handle an incoming request.
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
