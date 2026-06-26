<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ 既存の注文があればスキップ
        if (Order::exists()) {
            $this->command->warn('⚠ ダミー注文はすでに存在しています。');
            return;
        }

        // ✅ 先に商品を1件取得（product_id 用）
        $product = Product::first();

        if (!$product) {
            $this->command->error('❌ 商品が存在しないため OrderSeeder を中断します。');
            return;
        }

        // ✅ 注文作成
        $order = Order::create([
            'order_number' => 'TEST-' . strtoupper(str()->random(8)),

            'customer_name' => '山田 太郎',
            'customer_email' => 'test-order@example.com',
            'customer_phone' => '090-1234-5678',
            'shipping_address' => '東京都渋谷区テスト町1-2-3',

            'delivery_method' => 'sagawa',

            'subtotal' => 3000,
            'shipping_fee' => 800,
            'total_amount' => 3800,

            'payment_method' => 'bank_transfer',
            'payment_status' => 'unpaid',

            'status' => 'pending',
        ]);

        // ✅ 注文商品（product_id を正しくセット）
        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id, // ← ここが最重要ポイント
            'product_name' => $product->name,
            'member_name'  => 'うり',
            'unit_price'   => 3000,
            'quantity'     => 1,
            'subtotal'     => 3000,
        ]);

        $this->command->info('✅ ダミー注文データを作成しました。');
    }
}
