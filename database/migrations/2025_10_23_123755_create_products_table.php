<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            // price in integer (e.g. yen). If you prefer decimals, change to decimal(10,2)
            $table->unsignedInteger('price')->default(0);
            $table->string('sku')->unique()->nullable();
            $table->integer('stock')->default(0);
            $table->json('images')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}
