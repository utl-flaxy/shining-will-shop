<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Config;

class CheckoutController extends Controller
{
    protected OrderService $orderService;
    protected StripeClient $stripe;

    public function __construct(OrderService $orderService)
    {
        $this->middleware('auth');
        $this->orderService = $orderService;
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout session for the current user's cart (session-based cart).
     * Expects cart stored in session('cart', []) with keys product_id => ['title','price','qty','image']
     */
    public function createSession(Request $request)
    {
        $user = Auth::user();
        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'カートが空です。商品を追加してください。');
        }

        // Pull shop config
        $shippingFee = (int) config('shop.shipping_fee', 0);
        $taxRate = (int) config('shop.tax_rate', 0);
        $taxInclusive = (bool) config('shop.tax_included', false);
        $currency = config('shop.currency', 'JPY');

        // Create order skeleton
        $shippingAddress = $request->input('shipping_address', null);
        $order = $this->orderService->createOrderFromCart($cart, $user->id, $shippingAddress, $taxInclusive, $shippingFee, $taxRate);

        // Build Stripe line items
        $lineItems = [];
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);

            $images = [];
            if ($product) {
                // attempt to build image url from S3 if images stored as paths
                if (! empty($product->images) && is_array($product->images) && count($product->images)) {
                    $path = $product->images[0];
                    try {
                        $images[] = Storage::disk('s3')->url($path);
                    } catch (\Throwable $e) {
                        // fallback: leave images empty
                    }
                } elseif (! empty($product->image)) {
                    try {
                        $images[] = Storage::disk('s3')->url($product->image);
                    } catch (\Throwable $e) {
                        //
                    }
                }
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => [
                        'title' => $item->title ?: ($product->title ?? 'Product'),
                        'images' => $images,
                    ],
                    'unit_amount' => (int) $item->price, // yen (no cents for JPY)
                ],
                'quantity' => (int) $item->qty,
            ];
        }

        // Add shipping as a line item (flat fee)
        if ($order->shipping_fee > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => [
                        'title' => 'Shipping Fee',
                    ],
                    'unit_amount' => (int) $order->shipping_fee,
                ],
                'quantity' => 1,
            ];
        }

        // Create Stripe Checkout Session
        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'customer_email' => $user->email,
            'line_items' => $lineItems,
            'success_url' => config('app.url') . '/orders/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/cart',
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $user->id,
            ],
            'client_reference_id' => (string) $user->id,
        ]);

        // Save stripe session id on the order so webhook can find it
        $order->stripe_session_id = $session->id;
        $order->save();

        // Return the session id to client (if SPA or AJAX)
        return response()->json(['id' => $session->id, 'checkout_url' => $session->url ?? null]);
    }
}
