<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Panels
    |--------------------------------------------------------------------------
    |
    | You may define multiple Filament panels for your application. Each panel
    | may have its own path, resources, widgets, pages, and configuration.
    |
    */

    'panels' => [

        'admin' => [
            /*
            |--------------------------------------------------------------------------
            | ID
            |--------------------------------------------------------------------------
            |
            | This is the unique identifier for the panel. It must match the ID
            | used in your `make:filament-panel` command.
            |
            */
            'id' => 'admin',

            /*
            |--------------------------------------------------------------------------
            | Path
            |--------------------------------------------------------------------------
            |
            | The URL path that will be used to access the panel.
            |
            */
            'path' => 'admin',

            /*
            |--------------------------------------------------------------------------
            | Domain
            |--------------------------------------------------------------------------
            |
            | You can set a specific domain for this panel if desired.
            | e.g. 'admin.example.com'
            |
            */
            'domain' => null,

            /*
            |--------------------------------------------------------------------------
            | Middleware
            |--------------------------------------------------------------------------
            |
            | Define middleware that should be applied to this panel.
            |
            */
            'middleware' => [
                'web',
                \Filament\Http\Middleware\Authenticate::class,
            ],

            /*
            |--------------------------------------------------------------------------
            | Auth Guard
            |--------------------------------------------------------------------------
            |
            | Define which auth guard Filament should use for authentication.
            |
            */
            'auth_guard' => 'web',

            /*
            |--------------------------------------------------------------------------
            | Home URL
            |--------------------------------------------------------------------------
            |
            | Define where users should be redirected after logging in.
            |
            */
            'home_url' => '/admin',

            /*
            |--------------------------------------------------------------------------
            | Branding
            |--------------------------------------------------------------------------
            |
            | Define the title and branding for the panel.
            |
            */
            'brand' => [
                'name' => env('APP_NAME', 'ShiningWill Admin'),
                'logo' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | Resources, Widgets, Pages
            |--------------------------------------------------------------------------
            |
            | Specify which Filament resources, widgets, and pages to register.
            |
            */
            'resources' => [],
            'pages' => [],
            'widgets' => [],

            /*
            |--------------------------------------------------------------------------
            | Sidebar
            |--------------------------------------------------------------------------
            |
            | Control sidebar behavior and appearance.
            |
            */
            'sidebar' => [
                'collapsible_on_desktop' => true,
            ],
        ],

        // 追加パネル例: admin_shining
        'admin_shining' => [
            'id' => 'admin_shining',
            'path' => 'admin_shining',
            'domain' => null,
            'middleware' => [
                'web',
                \Filament\Http\Middleware\Authenticate::class,
            ],
            'auth_guard' => 'web',
            'home_url' => '/admin_shining',
            'brand' => [
                'name' => 'Shining Will Admin',
                'logo' => null,
            ],
            'resources' => [],
            'pages' => [],
            'widgets' => [],
        ],

    ],

];
