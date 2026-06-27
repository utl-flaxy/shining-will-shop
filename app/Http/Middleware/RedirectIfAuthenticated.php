<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (function_exists('auth') && auth()->check()) {
            return redirect('/home');
        }

        return $next($request);
    }
}
