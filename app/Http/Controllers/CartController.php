<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // カート一覧
    public function index()
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('shop.cart', compact('cart', 'total'));
    }

    // カートに追加
    public function add(Request $request, Product $product)
    {
        $cart = session('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', '商品をカートに追加しました！');
    }
}
