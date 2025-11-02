<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 安全のため Schema::hasColumn で存在確認してから追加
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'price')) {
                // JPY を想定して整数（円）で保持
                $table->integer('price')->default(0)->after('description');
            }
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'images')) {
                $table->json('images')->nullable()->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'images')) {
                $table->dropColumn('images');
            }
            if (Schema::hasColumn('products', 'stock')) {
                $table->dropColumn('stock');
            }
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('products', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('products', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};