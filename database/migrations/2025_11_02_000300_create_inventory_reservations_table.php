<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index(); // Checkout session or cart session identifier
            $table->integer('quantity')->default(1);
            $table->timestamp('expires_at')->nullable()->index();
            $table->string('status')->default('reserved'); // reserved, confirmed, released
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_reservations');
    }
};