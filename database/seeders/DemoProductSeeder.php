<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        echo "🌱 Start DemoProductSeeder...\n";

        /*
        |--------------------------------------------------------------------------
        | アイドルカテゴリ作成
        |--------------------------------------------------------------------------
        */

        $idolCategoriesData = [
            [
                'name' => 'Bety',
                'image' => 'categories/bety.jpg',
            ],
            [
                'name' => 'てぃあむ',
                'image' => 'categories/thiam.jpg',
            ],
            [
                'name' => 'ソロタレント',
                'image' => 'categories/solo.jpg',
            ],
        ];

        $idolCategories = [];

        foreach ($idolCategoriesData as $data) {

            $category = Category::updateOrCreate(
                ['name' => $data['name']],
                [
                    'image' => $data['image'],
                ]
            );

            $idolCategories[] = $category;

            echo "✅ Category: {$category->name}\n";
        }

        /*
        |--------------------------------------------------------------------------
        | 各カテゴリに商品を3件ずつ作成
        |--------------------------------------------------------------------------
        */

        foreach ($idolCategories as $category) {

            for ($i = 1; $i <= 3; $i++) {

                $product = Product::updateOrCreate(
                    [
                        'sku' => strtoupper(substr($category->name, 0, 3)) . "-00{$i}",
                    ],
                    [
                        'name' => "{$category->name} グッズ {$i}",
                        'description' => "{$category->name} オフィシャルグッズ {$i}",
                        'price' => rand(1500, 5000),
                        'stock' => rand(10, 100),
                        'category_id' => $category->id,
                        'is_active' => true,
                        'is_published' => true,
                    ]
                );

                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'sort_order' => 0,
                    ],
                    [
                        'url' => 'products/default.jpg',
                    ]
                );

                echo "  └ 商品作成: {$product->name}\n";
            }
        }

        echo "🎉 DemoProductSeeder finished successfully.\n";
    }
}
