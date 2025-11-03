<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | アプリケーション名
    |--------------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | 環境（local / production）
    |--------------------------------------------------------------------------
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | デバッグモード
    |--------------------------------------------------------------------------
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | アプリケーションURL
    |--------------------------------------------------------------------------
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | タイムゾーン
    |--------------------------------------------------------------------------
    */

    'timezone' => 'Asia/Tokyo',

    /*
    |--------------------------------------------------------------------------
    | ロケール設定
    |--------------------------------------------------------------------------
    */

    'locale' => 'ja',

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker 言語設定
    |--------------------------------------------------------------------------
    */

    'faker_locale' => 'ja_JP',

    /*
    |--------------------------------------------------------------------------
    | 暗号化キー
    |--------------------------------------------------------------------------
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | 自動ロードされるサービスプロバイダ
    |--------------------------------------------------------------------------
    |
    | ここにリストされたサービスプロバイダは、アプリケーションの
    | 起動時に自動的にロードされます。
    |
    */

    'providers' => [

        /*
        |--------------------------------------------------------------------------
        | Laravel Framework Service Providers...
        |--------------------------------------------------------------------------
        */

        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        App\Providers\FilamentServiceProvider::class,

        /*
        |--------------------------------------------------------------------------
        | サードパーティプロバイダ
        |--------------------------------------------------------------------------
        */

        // Filament 管理画面（v3対応）
        App\Providers\Filament\AdminShiningPanelProvider::class,

        /*
        |--------------------------------------------------------------------------
        | アプリケーション固有のサービスプロバイダ
        |--------------------------------------------------------------------------
        */

        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\Filament\AdminPanelProvider::class,
        App\Providers\Filament\AdminShiningPanelProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | クラスエイリアス
    |--------------------------------------------------------------------------
    */

    'aliases' => Facade::defaultAliases()->merge([
        // ここにカスタムエイリアスを追加可能
    ])->toArray(),

    App\Providers\FilamentWidgetsServiceProvider::class,


];
