<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Log;

class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        echo "🌱 Start DemoProductSeeder...\n";

        // -----------------------------------------
        // カテゴリ作成
        // -----------------------------------------
        $baseCategories = [
            ['name' => 'Electronics'],
            ['name' => 'Books'],
            ['name' => 'Others'],
        ];

        foreach ($baseCategories as $data) {
            Category::create($data);
        }
        echo "✅ Base categories created.\n";

        // --- 新カテゴリ（Bety, てぃあむ, ソロタレント） ---
        $idolCategoriesData = [
            ['name' => 'Bety', 'image' => 'categories/bety.jpg'],
            ['name' => 'てぃあむ', 'image' => 'categories/thiam.jpg'],
            ['name' => 'ソロタレント', 'image' => 'categories/solo.jpg'],
        ];

        $idolCategories = [];
        foreach ($idolCategoriesData as $data) {
            $idolCategories[] = Category::create($data);
            echo "  - Created category: {$data['name']}\n";
        }

        // -----------------------------------------
        // 固定商品（旧カテゴリ用）
        // -----------------------------------------
        $electronics = Category::where('name', 'Electronics')->first();
        $books = Category::where('name', 'Books')->first();

        $products = [
            [
                'name' => 'Laptop Computer',
                'description' => 'High-performance laptop for professionals',
                'price' => 150000,
                'category_id' => $electronics->id,
                'stock' => 10,
                'is_published' => true,
                'is_active' => true,
                'sku' => 'LAP-001',
            ],
            [
                'name' => 'Programming Book',
                'description' => 'Learn modern programming techniques',
                'price' => 3000,
                'category_id' => $books->id,
                'stock' => 50,
                'is_published' => true,
                'is_active' => true,
                'sku' => 'BOOK-001',
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse for daily use',
                'price' => 2500,
                'category_id' => $electronics->id,
                'stock' => 30,
                'is_published' => true,
                'is_active' => true,
                'sku' => 'MOU-001',
            ],
        ];

        foreach ($products as $data) {
            $product = Product::create($data);
            echo "  - Created base product: {$data['name']}\n";

            // ✅ 修正版（カラム名: url / sort_order）
            ProductImage::create([
                'product_id' => $product->id,
                'url' => 'products/sample.jpg',
                'sort_order' => 0,
            ]);
        }

        // -----------------------------------------
        // 各アイドルカテゴリにランダム商品を3件ずつ作成
        // -----------------------------------------
        foreach ($idolCategories as $category) {
            $createdProducts = Product::factory()->count(3)->create([
                'category_id' => $category->id,
                'is_active' => true,
                'is_published' => true,
            ]);

            foreach ($createdProducts as $p) {
                ProductImage::create([
                    'product_id' => $p->id,
                    'url' => 'products/default.jpg',
                    'sort_order' => 0,
                ]);
            }

            echo "  - Products created for category: {$category->name}\n";
        }

        echo "🎉 Seeder finished successfully.\n";
    }
}
