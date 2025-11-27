<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 購入者情報
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->text('shipping_address')->nullable();

            // 注文金額
            $table->integer('subtotal')->default(0);
            $table->integer('shipping_fee')->default(0);
            $table->integer('total_amount')->default(0);

            // ステータス管理
            $table->enum('status', [
                'pending',     // 入金待ち
                'paid',        // 入金確認
                'shipped',     // 発送済み
                'refunded',    // 返金済み
            ])->default('pending');

            // 発送情報
            $table->string('tracking_number')->nullable();
            $table->dateTime('shipped_at')->nullable();

            // 決済情報
            $table->string('payment_method')->default('bank_transfer');
            $table->string('payment_status')->default('unpaid');

            // タイムスタンプ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
