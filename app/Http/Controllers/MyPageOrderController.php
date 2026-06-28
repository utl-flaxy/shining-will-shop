<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageOrderController extends Controller
{
    /**
     * 注文履歴一覧
     */
    public function index()
    {
        $orders = Auth::user()
            ->orders()
            ->latest()
            ->get();

        return view('mypage.orders.index', compact('orders'));
    }

    /**
     * 注文詳細
     */
    public function show(Order $order)
    {
        // 他人の注文は閲覧不可
        abort_if($order->user_id !== Auth::id(), 403);

        // 注文明細・商品・バリエーションを取得
        $order->load([
            'items.product',
            'items.variant',
        ]);

        return view('mypage.orders.show', compact('order'));
    }
}
