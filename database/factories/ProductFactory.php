<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(6),
            'price' => $this->faker->numberBetween(1000, 100000),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'stock' => $this->faker->numberBetween(1, 50),
            'is_published' => true,
            'is_active' => true,
            'sku' => strtoupper($this->faker->bothify('SKU-###??')),

            // ❌ 'image' は削除（product_imagesテーブルで管理するため不要）
            // ✅ 下記のようにダミーデータとして必要ならSeederでProductImageを生成
        ];
    }
}
