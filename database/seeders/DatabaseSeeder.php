<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * ✅ デモ商品を投入（既存の処理そのまま）
         */
        Product::updateOrCreate(
            ['sku' => 'TEST-001'],
            [
                'sku' => 'TEST-001',
                'name' => 'Test Product',
                'description' => 'Seeded test product',
                'price' => 1000,
                'stock' => 100,
                'images' => null,
                'is_published' => true,
            ]
        );

        /**
         * ✅ 各種シーダーをまとめて実行
         * ※ 存在していない Seeder はコメントアウトしてもOK
         */
        $this->call([
            DemoProductSeeder::class, // 既存
            OrderSeeder::class,       // ✅ ここを追加（ダミー注文）
        ]);
    }
}
