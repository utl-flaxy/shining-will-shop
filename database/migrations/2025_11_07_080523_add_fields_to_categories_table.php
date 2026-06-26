<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // すでに存在する 'image' と 'description' は削除
            // $table->string('image')->nullable()->after('name');
            // $table->text('description')->nullable()->after('sort_order');

            $table->boolean('is_visible')->default(true)->after('name');
            $table->integer('sort_order')->default(0)->after('is_visible');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            //
        });
    }
};
