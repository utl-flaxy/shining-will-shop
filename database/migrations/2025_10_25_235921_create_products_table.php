<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 親商品
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 商品名
            $table->text('description')->nullable(); // 商品説明
            $table->boolean('is_stock_managed')->default(true); // 在庫管理ON/OFF
            $table->timestamp('sale_start_at')->nullable();
            $table->timestamp('sale_end_at')->nullable();
            $table->timestamps();
        });

        // 子バリエーション（メンバー別など）
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('member_name'); // 例: A子ちゃん
            $table->integer('price')->default(0);
            $table->integer('stock')->nullable(); // 在庫数
            $table->string('image_url')->nullable(); // 個別画像
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 商品画像（複数）
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('url'); // 画像URL
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
