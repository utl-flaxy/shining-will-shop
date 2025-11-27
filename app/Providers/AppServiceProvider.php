<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * ✅ コンソール実行時は Request が未バインドのことがあるので、
         *    APP_URL を使ってダミー Request を束縛しておく。
         *    これで UrlGenerator::__construct の $request=null 問題を回避。
         */
        if ($this->app->runningInConsole() && ! $this->app->bound('request')) {
            $url = config('app.url') ?? env('APP_URL', 'http://localhost');
            $this->app->instance('request', Request::create($url));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ CLI 実行時はここは何もしない
        if ($this->app->runningInConsole()) {
            return;
        }

        // Stripe キー確認は本番/ステージングのみ（任意）
        if ($this->app->environment(['production', 'staging'])) {
            $secret = config('services.stripe.secret') ?: env('STRIPE_SECRET');
            if (empty($secret)) {
                Log::error('⚠ STRIPE_SECRET is not configured.');
            }
        }
    }
}
