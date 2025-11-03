<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected function cart() { return session()->get('cart', []); }
    protected function save($cart) { session(['cart' => $cart]); }

    public function index()
    {
        $cart = $this->cart();
        // 金額再計算
        $items = [];
        $subtotal = 0;
        foreach ($cart as $productId => $row) {
            $product = Product::find($productId);
            if (!$product) continue;
            $qty = $row['qty'] ?? 1;
            $price = (int)($product->price);
            $items[] = ['product'=>$product, 'qty'=>$qty, 'price'=>$price, 'images'=>$product->images ?? []];
            $subtotal += $price * $qty;
        }
        $shipping = 0; // MVP：固定0円（必要なら後で変更）
        $total = $subtotal + $shipping;
        return view('front.cart.index', compact('items','subtotal','shipping','total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty' => ['nullable','integer','min:1']
        ]);
        $cart = $this->cart();
        $pid = (int)$request->product_id;
        $qty = max(1, (int)($request->qty ?? 1));
        $cart[$pid]['qty'] = ($cart[$pid]['qty'] ?? 0) + $qty;
        $this->save($cart);
        return redirect()->route('cart.index')->with('ok','カートに追加しました');
    }

    public function update(Request $request)
    {
        $request->validate(['lines'=>'required|array']);
        $cart = [];
        foreach ($request->lines as $pid => $qty) {
            $q = max(0, (int)$qty);
            if ($q>0) $cart[(int)$pid] = ['qty'=>$q];
        }
        $this->save($cart);
        return back()->with('ok','数量を更新しました');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id'=>'required|integer']);
        $cart = $this->cart();
        unset($cart[(int)$request->product_id]);
        $this->save($cart);
        return back()->with('ok','削除しました');
    }
}
