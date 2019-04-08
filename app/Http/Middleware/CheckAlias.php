<?php

namespace App\Http\Middleware;

use Closure;

class CheckAlias
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_null($request->user()->alias)) {
            return redirect('/link');
        }

        return $next($request);
    }
}
