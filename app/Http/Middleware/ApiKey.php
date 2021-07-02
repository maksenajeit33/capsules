<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->api_key != env("API_KEY_O") && $request->api_key != env("API_KEY_N"))
            return response()->json(['message' => 'Unauthenticated.']);

        return $next($request);
    }
}
