<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout
Route::post('/checkout/create', [CheckoutController::class, 'create'])->name('checkout.create');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Stripe webhook (must be POST)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

/*
|---------------------------------------------------------------------
| Simple product routes for local verification
|---------------------------------------------------------------------
|
| These are minimal closures that render the existing Blade views.
| If you already have controllers, remove or adapt these.
|
*/

use App\Models\Product;

Route::get('/', function () {
    return redirect()->route('products.index');
})->name('home');

Route::get('/products', function () {
    $products = Product::orderBy('id','desc')->get();
    return view('products.index', compact('products'));
})->name('products.index');

Route::get('/products/{product}', function (Product $product) {
    return view('products.show', compact('product'));
})->name('products.show');

