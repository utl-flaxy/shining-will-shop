<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 支払い方法を整える（card / bank_transfer / on_site）
            $table->string('payment_method')
                ->default('bank_transfer')
                ->comment('card / bank_transfer / on_site')
                ->change();

            // 支払いステータス（unpaid / paid / refunded / failed）
            $table->string('payment_status')
                ->default('unpaid')
                ->comment('unpaid / paid / refunded / failed')
                ->change();

            // 支払い関連
            $table->dateTime('paid_at')->nullable()->after('payment_status');
            $table->dateTime('bank_deposit_confirmed_at')->nullable()->after('paid_at');

            // 配送方法（佐川 / 現場渡し）
            $table->string('delivery_method')
                ->default('sagawa')
                ->comment('sagawa / pickup')
                ->after('shipping_address');

            // タレントへのメッセージ（備考欄）
            $table->text('note_to_talent')->nullable()->after('delivery_method');

            // 返金関連
            $table->integer('refunded_amount')->nullable()->after('total_amount');
            $table->dateTime('refunded_at')->nullable()->after('refunded_amount');
            $table->text('refund_reason')->nullable()->after('refunded_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'paid_at',
                'bank_deposit_confirmed_at',
                'delivery_method',
                'note_to_talent',
                'refunded_amount',
                'refunded_at',
                'refund_reason',
            ]);
        });
    }
};
