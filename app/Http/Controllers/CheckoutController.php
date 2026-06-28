<?php

namespace App\Http\Controllers;

use App\Mail\OrderCompletedMail;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Enums\OrderStatus;

class CheckoutController extends Controller
{
    /**
     * 注文確認画面
     */
    public function index()
    {
        $cart = session('cart', []);

        return view('checkout.index', compact('cart'));
    }

    /**
     * 注文開始（ポートフォリオ版）
     *
     * 決済APIは利用せず、
     * テスト注文として注文を確定します。
     */
    public function start(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'カートが空です。');
        }

        $total = collect($cart)->sum(
            fn ($item) => $item['price'] * $item['quantity']
        );

        return $this->finalizeOrder(
            $request,
            $cart,
            (int) $total,
            'test',
            'paid'
        );
    }

    /**
     * 注文保存
     */
    private function finalizeOrder(
        Request $request,
        array $cart,
        int $total,
        string $paymentMethod,
        string $paymentStatus
    ) {
        DB::beginTransaction();

        try {

            $user = Auth::user();

            $order = Order::create([

                'user_id' => $user?->id,

                'order_number' =>
                    'ORDER-'
                    . now()->format('YmdHis')
                    . '-'
                    . Str::upper(Str::random(6)),

                'customer_name' =>
                    $user?->name ?? 'ゲスト購入',

                'customer_email' =>
                    $user?->email ?? 'guest@example.com',

                'shipping_address' => 'テスト住所',

                'delivery_method' =>
                    $request->delivery_method ?? 'pickup',

                'subtotal' => $total,
                'shipping_fee' => 0,
                'total_amount' => $total,

                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,

                'status' => OrderStatus::Pending,

                'note_to_talent' =>
                    $request->note_to_talent,
            ]);

            foreach ($cart as $item) {

                OrderItem::create([

                    'order_id' => $order->id,

                    'product_id' => $item['id'],

                    'product_name' => $item['name'],

                    'unit_price' => $item['price'],

                    'quantity' => $item['quantity'],

                    'subtotal' =>
                        $item['price']
                        * $item['quantity'],
                ]);
            }

            // 在庫減算
            $order->decreaseStock();

            // 注文完了メール
            if ($user) {
                Mail::to($user->email)
                    ->send(new OrderCompletedMail($order));
            }

            DB::commit();

            session()->forget('cart');

            return redirect()
                ->route('checkout.success');

        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()
                ->route('checkout.cancel')
                ->with(
                    'error',
                    '注文確定エラー：' . $e->getMessage()
                );
        }
    }

    /**
     * 注文完了
     */
    public function success()
    {
        return view('checkout.success');
    }

    /**
     * 注文キャンセル
     */
    public function cancel()
    {
        return view('checkout.cancel');
    }
}
