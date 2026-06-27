<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) もし name カラムがなければ追加（既にある場合はスキップ）
        if (!Schema::hasColumn('products', 'name')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });
        }

        // 2) title が存在し、name に値が無い行があれば title の値をコピー
        if (Schema::hasColumn('products', 'title') && Schema::hasColumn('products', 'name')) {
            DB::statement('UPDATE products SET name = title WHERE (name IS NULL OR name = \'\')');
        }

        // 3) title カラムが存在するなら削除（本番ではバックアップ推奨）
        if (Schema::hasColumn('products', 'title')) {
            Schema::table('products', function (Blueprint $table) {
                // dropColumn は MySQL では通常問題ありません
                $table->dropColumn('title');
            });
        }

        // 4) name を NOT NULL にする（必要なら。ここでは既存データの整合をとった後に NOT NULL にします）
        if (Schema::hasColumn('products', 'name')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('name')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        // down: 元に戻す（name -> title にコピーして name を削除）
        if (!Schema::hasColumn('products', 'title')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('title')->nullable()->after('id');
            });
        }

        if (Schema::hasColumn('products', 'name') && Schema::hasColumn('products', 'title')) {
            DB::statement('UPDATE products SET title = name WHERE (title IS NULL OR title = \'\')');
        }

        if (Schema::hasColumn('products', 'name')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};