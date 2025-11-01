<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;

/**
 * Stripe webhook (stateless, CSRFなし)
 * VerifyCsrfToken:: は設定済みだが、API側はそもそもCSRFを通らない
 */
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
