<?php

namespace App\Http\Middleware;

use Closure;

class CheckStaff {
    /**
     * Check if the user is a staff member.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!$request->user()->isStaff) {
            flash('You do not have the permission to access this page.')->error();

            return redirect('/');
        }

        return $next($request);
    }
}
