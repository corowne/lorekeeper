<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParsePostRequestFields
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post')) {
            $excludedFields = ['_token', 'password', 'email', 'description', 'text'];
            $strippedFields = ['name', 'title'];
            
            $parsedFields = [];
            foreach ($request->except($excludedFields) as $key => $value) {
                if (in_array($key, $strippedFields)) { // we strip these since parse() doesn't remove HTML tags
                    $parsedFields[$key] = parse(strip_tags($value));
                } else {
                    $parsedFields[$key] = parse($value);
                }
                
            }

            $request->merge($parsedFields);
        }

        return $next($request);
    }
}
