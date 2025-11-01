<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | These settings determine how Laravel handles CORS requests. Adjust for
    | production: replace '*' with the allowed origins and set supports_credentials accordingly.
    |
    */

    // Paths that should be CORS-enabled
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],

    // Allowed methods; use ['*'] to allow all
    'allowed_methods' => ['*'],

    // Allowed origins; set to ['*'] to allow all or use env('CORS_ALLOWED_ORIGINS')
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    // Patterns to match origins
    'allowed_origins_patterns' => [],

    // Allowed headers; use ['*'] to allow all
    'allowed_headers' => ['*'],

    // Headers that are exposed to the browser
    'exposed_headers' => [],

    // How long the results of a preflight request can be cached (seconds)
    'max_age' => 0,

    // Whether to allow credentials (cookies, authorization headers, TLS client certificates)
    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', false),

];
