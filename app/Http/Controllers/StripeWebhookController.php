<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessStripeEvent;
use Carbon\Carbon;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        if (empty($sigHeader) || empty($secret)) {
            Log::warning('Stripe webhook: missing signature header or secret');
            return response('Missing signature or secret', 400);
        }

        // Parse Stripe-Signature header (format: t=timestamp,v1=signature[,v1=...])
        $pairs = [];
        foreach (explode(',', $sigHeader) as $part) {
            $kv = explode('=', trim($part), 2);
            if (count($kv) === 2) {
                $pairs[$kv[0]][] = $kv[1];
            }
        }

        if (!isset($pairs['t'][0]) || !isset($pairs['v1'])) {
            Log::warning('Stripe webhook: invalid signature header', ['header' => $sigHeader]);
            return response('Invalid signature header', 400);
        }

        $timestamp = (int) $pairs['t'][0];
        $signatures = $pairs['v1'];

        // check timestamp tolerance (default 5 minutes = 300s)
        $tolerance = (int) env('STRIPE_WEBHOOK_TOLERANCE', 300);
        if (abs(time() - $timestamp) > $tolerance) {
            Log::warning('Stripe webhook: timestamp outside tolerance', ['timestamp' => $timestamp, 'now' => time()]);
            return response('Timestamp outside tolerance', 400);
        }

        // compute expected signature
        $signedPayload = $timestamp . '.' . $payload;
        $expectedSig = hash_hmac('sha256', $signedPayload, $secret);

        $valid = false;
        foreach ($signatures as $sig) {
            if (hash_equals($expectedSig, $sig)) {
                $valid = true;
                break;
            }
        }
        if (! $valid) {
            Log::warning('Stripe webhook: signature verification failed', ['expected' => $expectedSig, 'header' => $sigHeader]);
            return response('Signature verification failed', 400);
        }

        // decode payload
        $event = json_decode($payload, true);
        if (! is_array($event) || empty($event['id'] ?? null)) {
            Log::warning('Stripe webhook: invalid JSON payload', ['payload' => $payload]);
            return response('Invalid payload', 400);
        }

        $eventId = $event['id'];

        // Idempotency: check processed_stripe_events for existing event_id
        $exists = DB::table('processed_stripe_events')->where('event_id', $eventId)->exists();
        if ($exists) {
            // Already processed — return 200 so Stripe doesn't retry
            Log::info('Stripe webhook: event already processed', ['event_id' => $eventId]);
            return response('Event already processed', 200);
        }

        // Save only minimal fields to avoid storing full raw payload (privacy / storage)
        $savePayload = [
            'id' => $event['id'] ?? null,
            'type' => $event['type'] ?? null,
            // store the main object id when present (safe small value)
            'object_id' => $event['data']['object']['id'] ?? null,
        ];

        try {
            DB::table('processed_stripe_events')->insert([
                'event_id'   => $eventId,
                'payload'    => json_encode($savePayload), // small JSON only
                'status'     => 'received',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: failed to record event', ['error' => $e->getMessage()]);
            // Still dispatch? safer to abort so Stripe retries.
            return response('Failed to record event', 500);
        }

        // Dispatch processing to a queued job (non-blocking)
        try {
            ProcessStripeEvent::dispatch($event);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: failed to dispatch job', ['error' => $e->getMessage(), 'event' => $eventId]);
            return response('Failed to dispatch job', 500);
        }

        return response('Received', 200);
    }
}
