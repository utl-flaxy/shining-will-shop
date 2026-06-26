<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            return $next($request);
        }

        // ✅ ownerルートだけ専用ログインへ
        if ($request->is('owner*')) {
            return redirect()->route('owner.login');
        }

        return redirect('/login');
    }
}
