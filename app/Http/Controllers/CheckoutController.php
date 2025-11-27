<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // ✅ 注文確認ページ
    public function index()
    {
        $cart = session('cart', []);
        return view('checkout.index', compact('cart'));
    }

    // ✅ 注文確定処理（Stripeは未使用）
    public function start(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'カートが空です');
        }

        // 合計金額計算
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        DB::beginTransaction();
        try {
            // 注文番号自動生成
            $orderNumber = 'ORDER-' . now()->format('YmdHis') . '-' . Str::random(4);

            // 注文を保存
            $order = Order::create([
                'order_number'    => $orderNumber,
                'customer_name'   => $request->name,
                'customer_email'  => $request->email,
                'shipping_address'=> $request->address,
                'status'          => 'pending',
                'total_amount'    => $total,
            ]);

            DB::commit();

            // カートをクリア
            session()->forget('cart');

            return redirect()->route('checkout.success')->with('success', '注文が確定しました！');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.cancel')->with('error', '注文処理に失敗しました: ' . $e->getMessage());
        }
    }

    // ✅ 成功ページ
    public function success()
    {
        return '<h2>ご注文ありがとうございました！</h2>';
    }

    // ✅ キャンセルページ
    public function cancel()
    {
        return '<h2>お支払いがキャンセルされました</h2>';
    }
}
