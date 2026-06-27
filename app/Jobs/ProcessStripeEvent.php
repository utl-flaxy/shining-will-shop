<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

                default:
                    Log::info('Job: Unhandled Stripe event', ['type' => $type, 'stripe_event_id' => $id]);
            }

            // 成功時に processed_at を更新
            if ($id) {
                DB::table('stripe_events')->where('stripe_event_id', $id)->update([
                    'processed_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Job: exception while processing stripe event: ' . $e->getMessage(), ['stripe_event_id' => $id]);
            throw $e;
        }
    }
}
