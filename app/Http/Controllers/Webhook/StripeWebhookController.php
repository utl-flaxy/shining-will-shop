<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            return response('Invalid', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $sid = $session['id'] ?? null;
            if ($sid) {
                $order = Order::where('stripe_session_id', $sid)->first();
                if ($order) {
                    $order->update([
                        'status' => 'paid',
                        'payload' => array_merge($order->payload ?? [], ['webhook_session_completed' => $session]),
                    ]);
                }
            }
        }

        return response('OK', 200);
    }
}
