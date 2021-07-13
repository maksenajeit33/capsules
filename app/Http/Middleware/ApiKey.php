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
        if($request->api_key != "5c4e878thbg5n4j54ii7sx4q5xad4" && $request->api_key != "45reg4rhe54bgr4eryki58fqz5f5t")
            return response()->json(['message' => 'Unauthenticated.'], 401);

        return $next($request);
    }
}
