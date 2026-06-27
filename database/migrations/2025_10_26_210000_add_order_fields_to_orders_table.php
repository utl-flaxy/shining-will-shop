<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add necessary columns to existing orders table if missing.
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->integer('subtotal')->default(0);
                $table->integer('shipping_fee')->default(0);
                $table->integer('tax_amount')->default(0);
                $table->integer('total_amount')->default(0);
                $table->string('currency', 8)->default('JPY');
                $table->string('status')->default('pending'); // pending, paid, processing, completed, cancelled, failed
                $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
                $table->string('stripe_session_id')->nullable()->index();
                $table->string('stripe_payment_intent_id')->nullable()->index();
                $table->json('shipping_address')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('orders', 'subtotal')) {
                $table->integer('subtotal')->default(0)->after('user_id');
            }
            if (! Schema::hasColumn('orders', 'shipping_fee')) {
                $table->integer('shipping_fee')->default(0)->after('subtotal');
            }
            if (! Schema::hasColumn('orders', 'tax_amount')) {
                $table->integer('tax_amount')->default(0)->after('shipping_fee');
            }
            if (! Schema::hasColumn('orders', 'total_amount')) {
                $table->integer('total_amount')->default(0)->after('tax_amount');
            }
            if (! Schema::hasColumn('orders', 'currency')) {
                $table->string('currency', 8)->default('JPY')->after('total_amount');
            }
            if (! Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('pending')->after('currency');
            }
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('status');
            }
            if (! Schema::hasColumn('orders', 'stripe_session_id')) {
                $table->string('stripe_session_id')->nullable()->index()->after('payment_status');
            }
            if (! Schema::hasColumn('orders', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable()->index()->after('stripe_session_id');
            }
            if (! Schema::hasColumn('orders', 'shipping_address')) {
                $table->json('shipping_address')->nullable()->after('stripe_payment_intent_id');
            }
            if (! Schema::hasColumn('orders', 'meta')) {
                $table->json('meta')->nullable()->after('shipping_address');
            }
            if (! Schema::hasColumn('orders', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // Do not drop orders table in down in case it existed before - remove only added columns if safe.
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('orders', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }
            if (Schema::hasColumn('orders', 'stripe_payment_intent_id')) {
                $table->dropColumn('stripe_payment_intent_id');
            }
            if (Schema::hasColumn('orders', 'stripe_session_id')) {
                $table->dropColumn('stripe_session_id');
            }
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('orders', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('orders', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('orders', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
            if (Schema::hasColumn('orders', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
            if (Schema::hasColumn('orders', 'shipping_fee')) {
                $table->dropColumn('shipping_fee');
            }
            if (Schema::hasColumn('orders', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};