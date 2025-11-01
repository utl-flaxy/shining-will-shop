<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('products.index');
});

// Product listing / detail
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/cart', [ProductController::class, 'cartIndex'])->name('cart.index');
Route::post('/cart/add', [ProductController::class, 'cartAdd'])->name('cart.add');
Route::post('/cart/update', [ProductController::class, 'cartUpdate'])->name('cart.update');
Route::post('/cart/remove', [ProductController::class, 'cartRemove'])->name('cart.remove');
Route::post('/cart/clear', [ProductController::class, 'cartClear'])->name('cart.clear');

// Include checkout/webhook routes if present
if (file_exists(__DIR__ . '/checkout_routes.php')) {
    require __DIR__ . '/checkout_routes.php';
}

/*
|--------------------------------------------------------------------------
| TEMP: accept Stripe webhook at /api/stripe/webhook (no CSRF) for local testing
|--------------------------------------------------------------------------
| Note: This is a temporary local helper. Prefer routes/api.php for API/webhook
| routes in a permanent setup. Remove this block when done testing.
*/
use App\Http\Controllers\StripeWebhookController;

if (app()->environment('testing') && file_exists(__DIR__.'/testing_auth.php')) {
    require __DIR__.'/testing_auth.php';
}
