<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Environment;
use Square\Models\CreateCheckoutRequest;
use Square\Models\Money;
use Square\Models\CreateOrderRequest;
use Square\Models\Order;
use Square\Models\OrderLineItem;

class SquareCheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'カートが空です');
        }

        $client = new SquareClient([
            'accessToken' => config('services.square.access_token'),
            'environment' => Environment::SANDBOX,
        ]);

        // ✅ 商品行作成
        $lineItems = [];
        foreach ($cart as $item) {
            $qty = $item['qty'] ?? $item['quantity'] ?? 1;

            $lineItems[] = new OrderLineItem(
                (string) $qty,
                $item['name'] ?? '商品'
            );
        }

        // ✅ Square注文作成
        $order = new Order(
            config('services.square.location_id'),
            $lineItems
        );

        $orderRequest = new CreateOrderRequest();
        $orderRequest->setOrder($order);

        $ordersApi = $client->getOrdersApi();
        $orderResponse = $ordersApi->createOrder($orderRequest);

        if ($orderResponse->isError()) {
            return back()->with('error', 'Square注文作成に失敗しました');
        }

        $squareOrderId = $orderResponse->getResult()->getOrder()->getId();

        // ✅ Checkout（カード入力画面）を作成
        $checkoutApi = $client->getCheckoutApi();

        $money = new Money();
        $money->setAmount(1); // 仮（Square側はオーダー金額を見る）
        $money->setCurrency('JPY');

        $checkoutRequest = new CreateCheckoutRequest(
            uniqid(),
            $money
        );

        $checkoutRequest->setOrderId($squareOrderId);
        $checkoutRequest->setRedirectUrl(route('square.success'));

        $checkoutResponse = $checkoutApi->createCheckout(
            config('services.square.location_id'),
            $checkoutRequest
        );

        if ($checkoutResponse->isError()) {
            return back()->with('error', 'Square決済画面の作成に失敗しました');
        }

        // ✅ ここが本命：カード入力画面へリダイレクト
        return redirect(
            $checkoutResponse->getResult()->getCheckout()->getCheckoutPageUrl()
        );
    }

    public function success()
    {
        session()->forget('cart');
        return view('shop.square-success');
    }

    public function cancel()
    {
        return view('shop.square-cancel');
    }
}
