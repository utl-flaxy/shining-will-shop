<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sku')) {
                // nullable + unique にして既存データとの互換性を保ちます
                $table->string('sku')->nullable()->unique()->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sku')) {
                // dropUnique は配列でインデックス名を渡します
                $table->dropUnique(['sku']);
                $table->dropColumn('sku');
            }
        });
    }
};