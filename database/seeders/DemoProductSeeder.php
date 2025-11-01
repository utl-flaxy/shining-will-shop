<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class DemoProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'sku' => 'SW-TSHIRT-NV-001',
                'title' => 'Shining Tシャツ (ネイビー)',
                'description' => '柔らかいコットン、サイズ S/M/L。シンプルなロゴ入り。',
                'price' => 2500,
                'stock' => 50,
                'images' => ['products/tshirt-navy-1.jpg', 'products/tshirt-navy-2.jpg'],
                'is_published' => true,
            ],
            [
                'sku' => 'SW-MUG-WT-001',
                'title' => 'Shining マグカップ (ホワイト)',
                'description' => '陶器製、350ml。ロゴプリント入り。',
                'price' => 1200,
                'stock' => 80,
                'images' => ['products/mug-white-1.jpg'],
                'is_published' => true,
            ],
        ];

        foreach ($products as $data) {
            // images は JSON カラムなので配列をそのまま入れられる（casts が array である前提）
            Product::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );
        }
    }
}
