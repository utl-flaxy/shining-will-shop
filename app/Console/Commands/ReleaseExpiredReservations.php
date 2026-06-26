<?php

namespace App\Console\Commands;

use App\Models\InventoryReservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'inventory:release-expired';
    protected $description = 'Release expired inventory reservations';

    public function handle()
    {
        $now = Carbon::now();
        $expired = InventoryReservation::where('status', 'reserved')
            ->where('expires_at', '<', $now)
            ->get();

        $this->info('Found ' . $expired->count() . ' expired reservations');

        foreach ($expired as $res) {
            $res->update(['status' => 'released']);
            // Optionally notify user/admin
        }

        return 0;
    }
}