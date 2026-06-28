<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CLI実行時のダミーRequest
        |--------------------------------------------------------------------------
        */

        if ($this->app->runningInConsole() && ! $this->app->bound('request')) {

            $url = config('app.url') ?? env('APP_URL', 'http://localhost');

            $this->app->instance(
                'request',
                Request::create($url)
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Observer登録
        |--------------------------------------------------------------------------
        */

        Order::observe(OrderObserver::class);

        /*
        |--------------------------------------------------------------------------
        | CLIではStripe確認しない
        |--------------------------------------------------------------------------
        */

        if ($this->app->runningInConsole()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Stripe設定確認
        |--------------------------------------------------------------------------
        */

        if ($this->app->environment(['production', 'staging'])) {

            $secret = config('services.stripe.secret')
                ?: env('STRIPE_SECRET');

            if (empty($secret)) {
                Log::error('⚠ STRIPE_SECRET is not configured.');
            }
        }
    }
}
