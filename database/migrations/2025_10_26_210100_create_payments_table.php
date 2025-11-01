<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('provider')->default('stripe');
            $table->string('provider_payment_id')->nullable()->index(); // stripe payment_intent id
            $table->integer('amount')->default(0);
            $table->string('currency', 8)->default('JPY');
            $table->string('status')->default('pending'); // pending, succeeded, failed, refunded
            $table->json('meta')->nullable();
            $table->timestamps();

            // foreign key optional - add if desired and DB integrity is wanted
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};