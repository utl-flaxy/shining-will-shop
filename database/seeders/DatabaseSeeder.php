<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者アカウント（存在しなければ作成）
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // テストユーザ（重複を避ける）
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // サンプル商品（products テーブルは title を持っているため title を使う）
        Product::firstOrCreate(
            ['sku' => 'TEST-001'],
            [
                'title' => 'Test Product',
                'price' => 1000,
                'description' => 'Seeded test product',
                'stock' => 100,
                // images フィールドが JSON/string の場合は適宜調整
                'images' => null,
                'is_published' => true,
            ]
        );

        // 必要なら追加の factory 生成はここで（ユニーク重複を避ける）
        // Product::factory()->count(10)->create();
    }
}
