<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);

        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1);

        $cart = Session::get('cart', []);
        $key = (string) $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => (int) $product->price,
                'quantity' => $quantity,
            ];
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'カートに追加しました');
    }

    public function update(Request $request)
    {
        $cart = Session::get('cart', []);

        $key = $request->input('key');
        $qty = max(1, (int) $request->input('quantity'));

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $qty;
        }

        Session::put('cart', $cart);

        return response()->json(['success' => true]);
    }

    public function remove(Request $request)
    {
        $cart = Session::get('cart', []);
        $key = $request->input('key');

        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', '商品を削除しました');
    }
}
