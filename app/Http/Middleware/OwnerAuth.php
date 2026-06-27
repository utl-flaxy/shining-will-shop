<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('owner')->check()) {
            return redirect()->route('owner.login');
        }

        return $next($request);
    }
}
