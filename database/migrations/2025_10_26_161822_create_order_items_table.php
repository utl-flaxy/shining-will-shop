<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 既に order_items テーブルが存在する場合は作成をスキップ
        if (Schema::hasTable('order_items')) {
            return;
        }

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('qty')->default(1);
            $table->integer('price')->default(0); // 単価（整数で保持）
            $table->timestamps();

            // 必要なら外部キー制約を追加（既存テーブルとの整合に注意）
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
