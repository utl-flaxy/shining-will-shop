<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->integer('shipping_cost')->default(0);
            $table->integer('total_amount')->default(0); // JPY: 最小単位（円）
            $table->string('status')->default('pending'); // pending|paid|canceled
            $table->string('stripe_session_id')->nullable();
            $table->json('payload')->nullable(); // Stripeレスポンスなど
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
