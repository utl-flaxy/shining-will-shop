<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // デモ商品を投入
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

        $this->call([
            DemoProductSeeder::class,
        ]);
    }
}
