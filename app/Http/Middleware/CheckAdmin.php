<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdmin
{
    /**
     * Redirect non-admins to the home page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->isAdmin) {
            flash('You do not have the permission to access this page.')->error();
            return redirect('/');
        }

        return $next($request);
    }
}
