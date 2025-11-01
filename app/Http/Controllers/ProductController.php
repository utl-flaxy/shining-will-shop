<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 商品一覧（公開された商品をページネーション）
    public function index()
    {
        $products = Product::where('is_published', true)->latest()->paginate(12);

        return view('products.index', compact('products'));
    }

    // 商品詳細
    public function show(Product $product)
    {
        // もし unpublished を非表示にしたい場合:
        // abort_unless($product->is_published, 404);

        return view('products.show', compact('product'));
    }

    // カート表示
    public function cartIndex()
    {
        $cart = session('cart', []);
        $items = collect($cart)->map(function ($item, $productId) {
            $item['product_id'] = $productId;
            $item['subtotal'] = $item['price'] * $item['qty'];
            return $item;
        })->values();

        $total = $items->sum('subtotal');

        return view('cart.index', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    // カートに追加（POST）
    public function cartAdd(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        $qty = $data['qty'] ?? 1;
        $product = Product::findOrFail($data['product_id']);

        $cart = session('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $qty;
        } else {
            // images は配列として保存している想定。表示用に先頭を使う。
            $image = null;
            if (is_array($product->images) && count($product->images)) {
                $image = $product->images[0];
            }

            $cart[$product->id] = [
                'title' => $product->title,
                'price' => (int) $product->price,
                'qty' => $qty,
                'image' => $image, // 保存パス（storage に保存されている想定）
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'カートに追加しました。');
    }

    // カート内数量更新（POST）
    public function cartUpdate(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);

        $productId = (string) $data['product_id'];
        if (! isset($cart[$productId])) {
            return back()->with('error', 'カートに該当商品がありません。');
        }

        if ($data['qty'] <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['qty'] = $data['qty'];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'カートを更新しました。');
    }

    // カートから削除（POST）
    public function cartRemove(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $cart = session('cart', []);
        $productId = (string) $data['product_id'];

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        }

        return back()->with('success', '商品をカートから削除しました。');
    }

    // カートを空にする（POST）
    public function cartClear()
    {
        session()->forget('cart');
        return back()->with('success', 'カートを空にしました。');
    }
}
