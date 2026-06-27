<?php

namespace App\Http\Controllers;

use App\Mail\OrderCompletedMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\SquarePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;

class CheckoutController extends Controller
{
    /**
     * ✅ 注文確認ページ
     */
    public function index()
    {
        $cart = session('cart', []);
        return view('checkout.index', compact('cart'));
    }

    /**
     * ✅ 本番＆テスト共通：注文確定処理
     */
    public function start(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'カートが空です');
        }

        // ✅ 合計金額
        $total = collect($cart)->sum(
            fn ($item) => $item['price'] * $item['quantity']
        );

        // ✅ 0円なら Square を通さず「テスト注文」として確定
        if ((int)$total <= 0) {
            return $this->completeTestOrder($request, $cart, $total);
        }

        // ============================
        // ✅ ここから Square 本番決済
        // ============================

        if (! $request->square_nonce) {
            return redirect()->route('checkout.cancel')
                ->with('error', 'カード情報が取得できませんでした');
        }

        $client      = SquarePaymentService::client();
        $paymentsApi = $client->getPaymentsApi();

        $money = new Money();
        $money->setAmount($total);
        $money->setCurrency('JPY');

        try {
            $requestBody = new CreatePaymentRequest(
                $request->square_nonce,
                (string) Str::uuid()
            );

            $requestBody->setAmountMoney($money);
            $requestBody->setLocationId(config('services.square.location_id'));

            $response = $paymentsApi->createPayment($requestBody);

            if ($response->isError()) {
                return redirect()->route('checkout.cancel')
                    ->with('error', '決済に失敗しました');
            }

            return $this->finalizeOrder($request, $cart, $total, 'card', 'paid');

        } catch (\Exception $e) {
            return redirect()->route('checkout.cancel')
                ->with('error', '決済エラー: ' . $e->getMessage());
        }
    }

    /**
     * ✅ テスト購入専用（0円）
     */
    private function completeTestOrder(Request $request, array $cart, int $total)
    {
        return $this->finalizeOrder(
            $request,
            $cart,
            $total,
            'test',
            'paid'
        );
    }

    /**
     * ✅ 注文保存共通処理（本番・テスト共通）
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
            $orderNumber = 'ORDER-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

            $order = Order::create([
                'order_number'     => $orderNumber,
                'customer_name'    => 'テスト購入',
                'customer_email'   => 'test@example.com',
                'shipping_address' => 'テスト住所',
                'delivery_method'  => $request->delivery_method ?? 'pickup',

                'subtotal'     => $total,
                'shipping_fee' => 0,
                'total_amount' => $total,

                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'status'         => 'paid',
                'note_to_talent' => $request->note_to_talent ?? null,
            ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['id'],
                'product_name' => $item['name'],
                'unit_price'   => $item['price'],
                'quantity'     => $item['quantity'], // ✅ qty → quantity に修正
                'subtotal'     => $item['price'] * $item['quantity'],
            ]);
        }

            $order->decreaseStock();

            DB::commit();

            session()->forget('cart');

            return redirect()->route('checkout.success');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('checkout.cancel')
                ->with('error', '注文確定エラー: ' . $e->getMessage());
        }
    }

    public function success()
    {
        return view('checkout.success');
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}
