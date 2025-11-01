<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function getRecentSales($limit = 10)
    {
        return PaymentIntent::all(['limit' => $limit]);
    }

    public function refund($paymentIntentId)
    {
        try {
            $refund = Refund::create([
                'payment_intent' => $paymentIntentId,
            ]);
            return $refund;
        } catch (\Exception $e) {
            throw new \Exception('返金に失敗しました: ' . $e->getMessage());
        }
    }
}
