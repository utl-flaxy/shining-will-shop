<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite（テスト用）では SHOW INDEX が使えないためスキップ
        if (DB::getDriverName() === 'sqlite') {
            // SQLiteの場合は unique 制約を直接追加（既に存在しない場合のみ）
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'stripe_checkout_session_id')) {
                    // カラムがない場合は作成して制約を付与
                    $table->string('stripe_checkout_session_id')->nullable()->unique('ux_orders_stripe_checkout_session_id');
                } else {
                    // カラムがある場合は unique を再付与（SQLiteは既存unique確認が難しいため例外回避）
                    try {
                        $table->unique('stripe_checkout_session_id', 'ux_orders_stripe_checkout_session_id');
                    } catch (\Throwable $e) {
                        // ignore duplicate unique constraint for SQLite
                    }
                }
            });
            return;
        }

        // MySQL/PostgreSQLなどの通常環境
        try {
            $exists = DB::select("SHOW INDEX FROM orders WHERE Key_name = 'ux_orders_stripe_checkout_session_id'");
        } catch (\Throwable $e) {
            $exists = [];
        }

        if (empty($exists)) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'stripe_checkout_session_id')) {
                    $table->string('stripe_checkout_session_id')->nullable();
                }
                $table->unique('stripe_checkout_session_id', 'ux_orders_stripe_checkout_session_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // unique 制約が存在しても存在しなくても安全に実行される
            try {
                $table->dropUnique('ux_orders_stripe_checkout_session_id');
            } catch (\Throwable $e) {
                // ignore errors (SQLite用)
            }
        });
    }
};
