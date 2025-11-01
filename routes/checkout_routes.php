<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

// Checkout (auth required)
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/checkout/create-session', [CheckoutController::class, 'createSession'])
        ->name('checkout.create');
});

// Stripe webhook (no CSRF - temporary)
// ※ routes/api.php にも同一ルートがあるなら、こちらはコメントアウトして構文エラーを防止
