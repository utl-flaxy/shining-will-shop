<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InventoryReservation;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class CheckoutController extends Controller
{
    // Create Stripe Checkout session from current cart
    public function create(Request $request)
    {
        $sessionId = $request->session()->getId();
        $cart = Cart::where('session_id', $sessionId)->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'カートが空です'], 400);
        }

        // Check stock for all items and create reservations
        $reservations = [];
        foreach ($cart->items as $item) {
            /** @var Product $product */
            $product = $item->product;
            if ($product->stock < $item->quantity) {
                return response()->json(['error' => "在庫不足: {$product->name}"], 400);
            }
        }

        // Create reservations (transaction not strictly necessary but safer)
        foreach ($cart->items as $item) {
            $res = InventoryReservation::create([
                'product_id' => $item->product->id,
                'session_id' => $sessionId,
                'quantity' => $item->quantity,
                'expires_at' => Carbon::now()->addMinutes(15),
                'status' => 'reserved',
            ]);
            $reservations[] = $res->id;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $line_items = [];
        foreach ($cart->items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => $item->product->price,
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Stripe metadata: reservation ids joined by comma
        $metadata = [
            'reservation_ids' => implode(',', $reservations),
            'session_id' => $sessionId,
        ];

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => $metadata,
        ]);

        // Save session id into reservations
        InventoryReservation::whereIn('id', $reservations)->update(['session_id' => $session->id]);

        return response()->json(['id' => $session->id, 'url' => $session->url]);
    }

    public function success(Request $request)
    {
        return view('checkout.success', ['session_id' => $request->query('session_id')]);
    }

    public function cancel(Request $request)
    {
        // Optionally release reservations associated with this session immediately
        return view('checkout.cancel');
    }
}
