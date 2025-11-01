<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Development-friendly: if auth isn't configured, allow through.
        if (! function_exists('auth') || auth()->guest()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest('/login');
        }

        return $next($request);
    }
}
