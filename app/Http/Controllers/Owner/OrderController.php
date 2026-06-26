<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(20);

        return view('owner.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // 関連の orderItems などがあれば with() でロードしてもOK
        $order->loadMissing('items.product');

        return view('owner.orders.show', compact('order'));
    }
}
