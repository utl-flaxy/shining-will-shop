<?php

namespace App\Http\Controllers;

use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }
        // ここから追加（イベント処理の前に一度だけ登録して重複を防ぐ）
        $eventId = $event->id ?? null;
        if ($eventId) {
            try {
                \DB::table('processed_stripe_events')->insert([
                    'event_id'   => $eventId,
                    'type'       => $event->type ?? null,
                    'payload'    => json_encode($event->data->object ?? []),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // 重複（ユニーク制約）などで挿入に失敗した場合は既に処理済みと見なしてスキップ
                \Log::info('Stripe event already processed, skipping: ' . $eventId);
                return response('ok', 200);
            }
        }

        // Handle checkout.session.completed directly
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            Log::info('Stripe webhook received: checkout.session.completed', ['event_id' => $event->id]);
            $this->handleCheckoutCompleted($session);
        }
        // Also handle payment_intent.succeeded/created by looking up its checkout session
        elseif ($event->type === 'payment_intent.succeeded' || $event->type === 'payment_intent.created') {
            $pi = $event->data->object;
            Log::info('Stripe webhook received: ' . $event->type, ['payment_intent' => $pi->id, 'event_id' => $event->id]);

            try {
                Stripe::setApiKey(config('services.stripe.secret'));

                // Query Checkout Sessions by payment_intent
                $sessions = \Stripe\Checkout\Session::all([
                    'payment_intent' => $pi->id,
                    'limit' => 1,
                ]);

                if (!empty($sessions->data) && isset($sessions->data[0])) {
                    $session = $sessions->data[0];
                    Log::info('Found checkout.session for payment_intent', ['checkout_session' => $session->id]);
                    $this->handleCheckoutCompleted($session);
                } else {
                    Log::warning('No checkout.session found for payment_intent', ['payment_intent' => $pi->id]);
                }
            } catch (\Exception $e) {
                Log::error('Error while fetching Checkout Session for PaymentIntent: ' . $e->getMessage());
            }
        } else {
            Log::info('Unhandled stripe event: ' . $event->type);
        }

        return response('ok', 200);
    }

    protected function handleCheckoutCompleted($session)
    {
        $reservationIds = [];

        // Support metadata.reservation_ids OR fallback: use session->id or session->payment_intent
        if (!empty($session->metadata->reservation_ids ?? null)) {
            $reservationIds = explode(',', $session->metadata->reservation_ids);
        } else {
            // If no reservation_ids meta, try to find reservations by session id stored in DB
            $sessionId = $session->id ?? ($session->payment_intent ?? null);
            if ($sessionId) {
                $reservations = InventoryReservation::where('session_id', $sessionId)
                    ->where('status', 'reserved')
                    ->get();

                if ($reservations->isNotEmpty()) {
                    $reservationIds = $reservations->pluck('id')->map(fn($v) => (string)$v)->toArray();
                }
            }
        }

        if (empty($reservationIds)) {
            Log::warning('Checkout session completed but no reservations: ' . ($session->id ?? 'unknown'));
            return;
        }

        \DB::transaction(function () use ($reservationIds, $session) {
            // Idempotency check: skip if an order for this checkout session already exists
            if (!empty($session->id) && Order::where('stripe_checkout_session_id', $session->id)->exists()) {
                Log::info('Order already exists for checkout session, skipping: ' . $session->id);
                return;
            }

            $reservations = InventoryReservation::whereIn('id', $reservationIds)
                ->where('status', 'reserved')
                ->lockForUpdate()
                ->get();

            // Re-check stock for each product under lock
            foreach ($reservations as $reservation) {
                $product = Product::lockForUpdate()->find($reservation->product_id);
                if (!$product) {
                    $reservation->update(['status' => 'released']);
                    continue;
                }
                if ($product->stock < $reservation->quantity) {
                    // Not enough stock - release reservation and log
                    $reservation->update(['status' => 'released']);
                    \Log::warning("Reservation {$reservation->id} failed: insufficient stock for product {$product->id}");
                }
            }

            // After re-check, collect confirmed reservations
            $confirmed = $reservations->filter(function ($r) {
                $product = Product::find($r->product_id);
                return $product && $product->stock >= $r->quantity;
            });

            if ($confirmed->isEmpty()) {
                // No reservation can be fulfilled
                return;
            }

            // Create order
            $orderTotal = 0;
            $order = Order::create([
                'status' => 'paid',
                'payment_status' => 'paid',
                'stripe_checkout_session_id' => $session->id ?? null,
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'total_amount' => 0,
                'currency' => $session->currency ?? 'jpy',
            ]);

            foreach ($confirmed as $reservation) {
                $product = Product::lockForUpdate()->find($reservation->product_id);
                if ($product->stock >= $reservation->quantity) {
                    $product->stock -= $reservation->quantity;
                    $product->save();

                    $lineTotal = ($product->price ?? 0) * $reservation->quantity;
                    $orderTotal += $lineTotal;

                    // Ensure product_name and unit_price (and price) are saved to order_items
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $reservation->quantity,
                        'unit_price' => $product->price ?? 0,
                        'price' => $product->price ?? 0,
                    ]);

                    $reservation->update(['status' => 'confirmed']);
                } else {
                    $reservation->update(['status' => 'released']);
                }
            }

            $order->update(['total_amount' => $orderTotal]);

            // TODO: dispatch email jobs (order confirmation, admin notification)
        });
    }
}
