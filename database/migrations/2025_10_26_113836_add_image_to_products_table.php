<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add images column only if it does not already exist
        if (! Schema::hasColumn('products', 'images')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('images')->nullable()->after('price');
            });
        }
    }

    public function down(): void
    {
        // Drop the column only if it exists
        if (Schema::hasColumn('products', 'images')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('images');
            });
        }
    }
};
