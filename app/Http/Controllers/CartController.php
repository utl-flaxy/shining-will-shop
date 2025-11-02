<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // get or create cart by session_id (frontend should supply session id or use auth user)
    protected function getCartBySession(Request $request)
    {
        $sessionId = $request->session()->getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        return $cart;
    }

    public function index(Request $request)
    {
        $cart = $this->getCartBySession($request);
        $cart->load('items.product');
        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $cart = $this->getCartBySession($request);
        $product = Product::findOrFail($request->input('product_id'));
        $quantity = max(1, (int)$request->input('quantity', 1));

        $item = $cart->items()->where('product_id', $product->id)->first();
        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'added', 'cart' => $cart->load('items.product')]);
    }

    public function update(Request $request, $itemId)
    {
        $cart = $this->getCartBySession($request);
        $item = $cart->items()->findOrFail($itemId);
        $quantity = max(0, (int)$request->input('quantity', 1));
        if ($quantity === 0) {
            $item->delete();
            return response()->json(['message' => 'removed']);
        }
        $item->quantity = $quantity;
        $item->save();
        return response()->json(['message' => 'updated', 'item' => $item]);
    }

    public function remove(Request $request, $itemId)
    {
        $cart = $this->getCartBySession($request);
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();
        return response()->json(['message' => 'removed']);
    }
}
