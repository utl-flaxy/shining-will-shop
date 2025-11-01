<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value determines the root namespace that Livewire will use when
    | generating new component classes using the `make:livewire` Artisan
    | command. It is also used when referencing components by class name.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value determines the directory that Livewire will use to store
    | component views. It is used when generating views via Artisan.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | The default layout to wrap Livewire component views in. Set to null if
    | you want to disable wrapping Livewire views in a layout.
    |
    */

    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Configuration for temporary file uploads using Livewire's file upload
    | features. Files will be stored in a temporary directory before being
    | permanently moved.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,
        'rules' => null,
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => ['png', 'gif', 'bmp', 'svg', 'wav', 'mp4', 'mov', 'avi', 'wmv', 'mp3', 'm4a', 'jpg', 'jpeg', 'pdf'],
        'max_upload_time' => 5, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | CSRF Middleware
    |--------------------------------------------------------------------------
    |
    | Livewire uses Laravel's built-in CSRF protection system. If your setup
    | has custom CSRF handling, you may define your middleware here.
    |
    */

    'csrf' => [
        'middleware' => \App\Http\Middleware\VerifyCsrfToken::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be applied to every Livewire request. You can add
    | your own middleware here or override existing behavior.
    |
    */

    'middleware' => [
        'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Manifest Path
    |--------------------------------------------------------------------------
    |
    | The path to Livewire's manifest file for component autoloading. This is
    | typically cached in production for performance optimization.
    |
    */

    'manifest_path' => storage_path('framework/livewire-components.php'),

    /*
    |--------------------------------------------------------------------------
    | Inject Assets
    |--------------------------------------------------------------------------
    |
    | Whether Livewire's JavaScript assets should be automatically injected
    | into all responses containing a closing </head> tag.
    |
    */

    'inject_assets' => true,

    /*
    |--------------------------------------------------------------------------
    | App URL
    |--------------------------------------------------------------------------
    |
    | The base URL for your Livewire requests. Useful if using proxies or
    | tunneling tools (like ngrok) in development.
    |
    */

    'app_url' => env('APP_URL', '/'),

    /*
    |--------------------------------------------------------------------------
    | Legacy Model Binding
    |--------------------------------------------------------------------------
    |
    | Whether to use legacy model binding behavior. Disable unless you have
    | old components that depend on the previous binding system.
    |
    */

    'legacy_model_binding' => false,
];
