<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * This adds a runtime check that ensures a Stripe secret is configured for
     * HTTP requests (skips CLI and testing). If the secret is missing we log
     * an error and throw an exception so the problem is discovered early.
     *
     * @return void
     *
     * @throws \RuntimeException when STRIPE_SECRET / services.stripe.secret is not set
     */
    public function boot()
    {
        // Skip CLI (artisan) and automated tests
        if ($this->app->runningInConsole() || $this->app->environment('testing')) {
            return;
        }

        $secret = config('services.stripe.secret') ?: env('STRIPE_SECRET');

        if (empty($secret)) {
            // Log for visibility and fail fast so missing config is noticed immediately.
            Log::error('STRIPE_SECRET is not configured. Set STRIPE_SECRET in .env or configure services.stripe.secret.');
            throw new \RuntimeException('STRIPE_SECRET is not configured. Set STRIPE_SECRET in .env or configure services.stripe.secret.');
        }
    }
}
