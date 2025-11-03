<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Minimal PreventRequestsDuringMaintenance middleware (development stub).
 */
class PreventRequestsDuringMaintenance
{
    public function handle($request, Closure $next)
    {
        if (app()->has('encrypter') && app()->isDownForMaintenance()) {
            return response('Service Unavailable', 503);
        }

        return $next($request);
    }
}
