<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
 * Support both Laravel 12+ bootstrap style and Laravel <=10 style.
 * If the Application::configure(...) fluent API exists (Laravel 12+), use it.
 * Otherwise fall back to the traditional Application instantiation (Laravel 10 and below).
 */

if (method_exists(Application::class, 'configure')) {
    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware): void {
            //
        })
        ->withExceptions(function (Exceptions $exceptions): void {
            //
        })->create();
}

/*
 * Traditional Laravel <=10 bootstrap (ensure required contract bindings exist).
 */
$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
 |---------------------------------------------------------------------------
 | Bind Important Interfaces
 |---------------------------------------------------------------------------
 |
 | Here we bind the HTTP kernel, Console kernel and the Exception handler
 | that are used by the Laravel framework. Update the class names below
 | if your app uses different namespaces/locations.
 |
 */

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
