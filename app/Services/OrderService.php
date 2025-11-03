<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService
{
    /**
     * Finalize an order: reduce stock, mark paid, etc.
     * This is executed in a DB transaction.
     */
    public function finalizeOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            // reload items
            $order->load('items');

            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if (! $product) {
                    throw new Exception("Product #{$item->product_id} not found for order {$order->id}");
                }

                // Check stock if tracking
                if (isset($product->stock) && $product->stock !== null) {
                    if ($product->stock < $item->qty) {
                        // Not enough stock: throw or mark
                        throw new Exception("Insufficient stock for product {$product->id} ({$product->name})");
                    }
                    $product->stock = $product->stock - $item->qty;
                    $product->save();
                }
            }

            // mark payment status / order status
            $order->payment_status = 'paid';
            $order->status = 'processing';
            $order->save();

            return $order;
        });
    }

    /**
     * Create an order skeleton (pending) from session cart and current user.
     * Returns the created Order instance.
     */
    public function createOrderFromCart(array $cart, int $userId, array $shippingAddress = null, bool $taxInclusive = false, int $shippingFee = 0, int $taxRate = 0): Order
    {
        // cart is array keyed by product_id with ['title','price','qty','image'] etc or numeric
        // Calculate subtotal
        $subtotal = 0;
        foreach ($cart as $productId => $row) {
            $qty = isset($row['qty']) ? (int)$row['qty'] : 1;
            $price = isset($row['price']) ? (int)$row['price'] : 0;
            $subtotal += $price * $qty;
        }

        // tax calculation
        $taxAmount = 0;
        if ($taxRate > 0) {
            if ($taxInclusive) {
                // if included, extract tax portion: tax = subtotal - (subtotal / (1 + r))
                $taxAmount = (int) round($subtotal - ($subtotal / (1 + $taxRate / 100)));
            } else {
                $taxAmount = (int) round($subtotal * ($taxRate / 100));
            }
        }

        $total = $subtotal + $shippingFee + $taxAmount;

        $order = Order::create([
            'user_id' => $userId,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'currency' => 'JPY',
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_address' => $shippingAddress ? $shippingAddress : null,
        ]);

        // create items
        foreach ($cart as $productId => $row) {
            $qty = isset($row['qty']) ? (int)$row['qty'] : 1;
            $price = isset($row['price']) ? (int)$row['price'] : 0;
            $title = $row['title'] ?? $row['name'] ?? null;
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => (int)$productId,
                'title' => $title,
                'sku' => $row['sku'] ?? null,
                'price' => $price,
                'qty' => $qty,
                'subtotal' => $price * $qty,
                'meta' => $row['meta'] ?? null,
            ]);
        }

        return $order;
    }
}