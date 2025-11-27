<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerAuth
{
    public function handle(Request $request, Closure $next)
    {
        // 未ログインなら /owner/login へ
        if (! Auth::check()) {
            return redirect()->route('owner.login');
        }

        // is_admin フラグが無い / false なら強制ログアウト
        if (! Auth::user()->is_admin) {
            Auth::logout();
            return redirect()->route('owner.login')
                ->withErrors(['email' => '管理者のみアクセスできます。']);
        }

        return $next($request);
    }
}
