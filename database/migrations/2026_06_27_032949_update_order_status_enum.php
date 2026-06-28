<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite（テスト）ではスキップ
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE orders
            MODIFY status ENUM(
                'pending',
                'preparing',
                'shipped',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // SQLite（テスト）ではスキップ
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE orders
            MODIFY status ENUM(
                'pending',
                'paid',
                'shipped',
                'refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
