<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;

/**
 * Safe TrustProxies for development.
 *
 * This version avoids any compile-time reference to Symfony constants to
 * prevent "Undefined constant" errors in environments with differing HttpFoundation.
 * It disables forwarded header parsing (headers = 0). Adjust $proxies as needed.
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies = null;

    /**
     * Disable forwarded header parsing to avoid constant compatibility issues.
     *
     * @var int
     */
    protected $headers = 0;
}
