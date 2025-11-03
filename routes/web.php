<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\StripePaymentController; // 追加 (実装済みを想定)
use App\Http\Controllers\Front\ProductController as FrontProductController;
use App\Http\Controllers\Front\CartController as FrontCartController;
use App\Http\Controllers\Front\CheckoutController as FrontCheckoutController;
use Illuminate\Http\Request;
use App\Models\Product;

// Front-end Product Routes (with search/filter)
Route::get('/', [FrontProductController::class, 'index'])->name('store.index');
Route::get('/products', [FrontProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [FrontProductController::class, 'show'])->name('store.product');

// Front-end Cart Routes
Route::get('/cart', [FrontCartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [FrontCartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [FrontCartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [FrontCartController::class, 'remove'])->name('cart.remove');

// Front-end Checkout Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::post('/checkout/start', [FrontCheckoutController::class, 'start'])->name('checkout.start');
    Route::get('/checkout/success', [FrontCheckoutController::class, 'success'])->name('checkout.success');
});
Route::get('/checkout/cancel', [FrontCheckoutController::class, 'cancel'])->name('checkout.cancel');

// Stripe webhook
Route::post('/stripe/webhook', [\App\Http\Controllers\Webhook\StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Unthrottled webhook for local development
if (env("ENABLE_LOCAL_WEBHOOK_UNTHROTTLED", false)) {
    Route::post('stripe/webhook-unthrottled', [\App\Http\Controllers\Webhook\StripeWebhookController::class, 'handle'])
        ->withoutMiddleware('throttle:api');
}
