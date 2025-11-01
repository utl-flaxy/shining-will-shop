<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Jobs\ProcessStripeEvent;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret') ?: env('STRIPE_WEBHOOK_SECRET');

        if (empty($payload)) {
            Log::warning('Stripe webhook received empty payload');
            return response('Bad Request', 400);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret') ?: env('STRIPE_SECRET'));
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe signature verification failed: ' . $e->getMessage());
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook unexpected error: ' . $e->getMessage());
            return response('Server Error', 500);
        }

        // 冪等キー（Stripe event.id）
        $eventId = $event->id ?? null;
        if (!$eventId) {
            Log::warning('Stripe event missing id');
            return response('Bad Request', 400);
        }

        $type = $event->type ?? '';
        Log::info("Stripe webhook received: {$type}", ['event_id' => $eventId]);

        // 冪等チェックと永続化
        try {
            DB::beginTransaction();
            $exists = DB::table('stripe_events')->where('stripe_event_id', $eventId)->exists();
            if ($exists) {
                DB::commit();
                Log::info('Stripe event already processed, skipping', ['event_id' => $eventId]);
                return response('', 200);
            }

            DB::table('stripe_events')->insert([
                'stripe_event_id' => $eventId,
                'type' => $type,
                'payload' => json_encode($event->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to persist stripe event: ' . $e->getMessage(), ['event_id' => $eventId]);
            return response('Server Error', 500);
        }

        // ジョブに投げる（非同期処理）
        try {
            ProcessStripeEvent::dispatch($event->toArray());
        } catch (\Exception $e) {
            Log::error('Failed to dispatch job for stripe event: ' . $e->getMessage(), ['event_id' => $eventId]);
            // ここでは 200 を返す方針を採る場合もあります。運用で判断してください。
        }

        return response('', 200);
    }
}
