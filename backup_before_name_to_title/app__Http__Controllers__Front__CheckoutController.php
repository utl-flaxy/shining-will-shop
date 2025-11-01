<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class CheckoutController extends Controller
{
    public function start(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) return redirect()->route('cart.index')->with('error','カートが空です');

        // 注文下書き
        $subtotal = 0; $items=[];
        foreach ($cart as $pid => $row) {
            $product = Product::find($pid);
            if (!$product) continue;
            $qty = max(1, (int)($row['qty'] ?? 1));
            $price = (int)$product->price;
            $subtotal += $price * $qty;
            $items[] = compact('product','qty','price');
        }
        $shipping = 0;
        $total = $subtotal + $shipping;

        $order = Order::create([
            'user_id' => auth()->id(),
            'name' => auth()->user()->name ?? null,
            'email' => auth()->user()->email ?? null,
            'address' => null,
            'shipping_cost' => $shipping,
            'total_amount' => $total,
            'status' => 'pending',
        ]);
        foreach ($items as $it) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $it['product']->id,
                'product_name' => $it['product']->name,
                'price' => $it['price'],
                'quantity' => $it['qty'],
                'options' => null,
            ]);
        }

        // Stripe Checkout
        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

        $lineItems = [];
        foreach ($items as $it) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $it['product']->name],
                    'unit_amount' => $it['price'], // 円
                ],
                'quantity' => $it['qty'],
            ];
        }

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => ['order_id' => (string)$order->id],
        ]);

        $order->update(['stripe_session_id' => $session->id, 'payload' => ['checkout_session'=> $session->toArray()]]);
        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sid = $request->get('session_id');
        if (!$sid) return redirect()->route('store.index');

        $order = Order::where('stripe_session_id', $sid)->first();
        if ($order && $order->status !== 'paid') {
            // Webhookで確定が先行しない場合の保険（簡易）
            $order->update(['status' => 'paid']);
        }
        // 成功したらカート破棄
        session()->forget('cart');

        return view('front.checkout.success', compact('order'));
    }

    public function cancel()
    {
        return view('front.checkout.cancel');
    }
}
