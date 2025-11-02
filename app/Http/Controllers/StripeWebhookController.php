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

        $eventId = $event->id ?? null;

        // payment_intent の場合は事前に Checkout Session を取得しておく（外部呼び出しはトランザクション外で）
        $session = null;
        if (in_array($event->type, ['payment_intent.succeeded', 'payment_intent.created'])) {
            $pi = $event->data->object;
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $sessions = \Stripe\Checkout\Session::all([
                    'payment_intent' => $pi->id,
                    'limit' => 1,
                ]);
                if (!empty($sessions->data) && isset($sessions->data[0])) {
                    $session = $sessions->data[0];
                    Log::info('Found checkout.session for payment_intent', ['checkout_session' => $session->id]);
                } else {
                    Log::warning('No checkout.session found for payment_intent', ['payment_intent' => $pi->id]);
                }
            } catch (\Exception $e) {
                Log::error('Error while fetching Checkout Session for PaymentIntent: ' . $e->getMessage());
            }
        } elseif ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
        }

        // ここで、イベント登録と注文作成処理をDBトランザクションで囲む（原子性を確保）
        // 注意: 上で Stripe API 呼び出しは既に行っている（トランザクション外）
        if ($eventId) {
            try {
                DB::transaction(function () use ($event, $eventId, $session) {
                    // processed_stripe_events を先に登録する（UNIQUE 制約で重複防止）
                    DB::table('processed_stripe_events')->insert([
                        'event_id'   => $eventId,
                        'type'       => $event->type ?? null,
                        'payload'    => json_encode($event->data->object ?? []),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 処理を実行（session が取れていれば注文処理へ）
                    if ($session) {
                        $this->handleCheckoutCompleted($session);
                    } else {
                        Log::info('No checkout session available for event: ' . $eventId . ' type: ' . ($event->type ?? 'unknown'));
                    }
                }, 5); // 5 retries for deadlock
            } catch (\Illuminate\Database\QueryException $e) {
                // UNIQUE 制約で重複 → 既に処理済み
                Log::info('Stripe event already processed (or DB error): ' . $eventId . ' - ' . $e->getMessage());
                return response('ok', 200);
            } catch (\Exception $e) {
                // その他の例外：ログに残して 500 を返す（Stripe は再送してくる）
                Log::error('Error processing stripe event ' . $eventId . ': ' . $e->getMessage());
                return response('Internal error', 500);
            }
        } else {
            Log::warning('Stripe event has no id, skipping.');
        }

        return response('ok', 200);
    }

    protected function handleCheckoutCompleted($session)
    {
        // （既存の実装をそのまま使用 — 内部でも DB::transaction を使っていますが、
        // Laravel のネストトランザクションは savepoint を使うため安全です）
        $reservationIds = [];
        if (!empty($session->metadata->reservation_ids ?? null)) {
            $reservationIds = explode(',', $session->metadata->reservation_ids);
        } else {
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
            if (!empty($session->id) && Order::where('stripe_checkout_session_id', $session->id)->exists()) {
                Log::info('Order already exists for checkout session, skipping: ' . $session->id);
                return;
            }

            $reservations = InventoryReservation::whereIn('id', $reservationIds)
                ->where('status', 'reserved')
                ->lockForUpdate()
                ->get();

            foreach ($reservations as $reservation) {
                $product = Product::lockForUpdate()->find($reservation->product_id);
                if (!$product) {
                    $reservation->update(['status' => 'released']);
                    continue;
                }
                if ($product->stock < $reservation->quantity) {
                    $reservation->update(['status' => 'released']);
                    \Log::warning("Reservation {$reservation->id} failed: insufficient stock for product {$product->id}");
                }
            }

            $confirmed = $reservations->filter(function ($r) {
                $product = Product::find($r->product_id);
                return $product && $product->stock >= $r->quantity;
            });

            if ($confirmed->isEmpty()) {
                return;
            }

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
        });
    }
}
