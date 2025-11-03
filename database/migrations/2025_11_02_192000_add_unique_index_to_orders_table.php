<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueIndexToOrdersTable extends Migration
{
    public function up()
    {
        // Ensure the stripe_checkout_session_id column exists; if not, add it (safe-guard)
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'stripe_checkout_session_id')) {
                $table->string('stripe_checkout_session_id')->nullable()->after('payment_status');
            }
        });

        // Add unique index if it doesn't exist
        $exists = DB::select("SHOW INDEX FROM orders WHERE Key_name = 'ux_orders_stripe_checkout_session_id'");
        if (empty($exists)) {
            // Use a prefix length to be safe with older MySQL/utf8mb4
            DB::statement('ALTER TABLE orders ADD UNIQUE INDEX ux_orders_stripe_checkout_session_id (stripe_checkout_session_id(191))');
        }
    }

    public function down()
    {
        $exists = DB::select("SHOW INDEX FROM orders WHERE Key_name = 'ux_orders_stripe_checkout_session_id'");
        if (!empty($exists)) {
            DB::statement('ALTER TABLE orders DROP INDEX ux_orders_stripe_checkout_session_id');
        }
    }
}