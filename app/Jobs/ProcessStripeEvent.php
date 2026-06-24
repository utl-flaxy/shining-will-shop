<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessStripeEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $event;

    public function __construct(array $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $type = $this->event['type'] ?? null;
        $id = $this->event['id'] ?? null;

        try {
            switch ($type) {
                case 'checkout.session.completed':
                    Log::info('Job: Handle checkout.session.completed', ['stripe_event_id' => $id]);
                    break;

                case 'invoice.paid':
                    Log::info('Job: Handle invoice.paid', ['stripe_event_id' => $id]);
                    break;

                case 'payment_intent.created':
                    Log::info('Job: Handle payment_intent.created', ['stripe_event_id' => $id]);
                    break;

                case 'payment_intent.succeeded':
                    Log::info('Job: Handle payment_intent.succeeded', ['stripe_event_id' => $id]);
                    break;

                case 'charge.succeeded':
                    Log::info('Job: Handle charge.succeeded', ['stripe_event_id' => $id]);
                    break;

                case 'charge.updated':
                    Log::info('Job: Handle charge.updated', ['stripe_event_id' => $id]);
                    break;

                default:
                    Log::info('Job: Unhandled Stripe event', ['type' => $type, 'stripe_event_id' => $id]);
            }

            if ($id) {
                DB::table('processed_stripe_events')->where('event_id', $id)->update([
                    'status' => 'processed',
                    'processed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Job: exception while processing stripe event: ' . $e->getMessage(), ['stripe_event_id' => $id]);
            throw $e;
        }
    }
}
