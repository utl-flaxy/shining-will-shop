<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * 商品一覧
     */
    public function index()
    {
        $products = Product::latest()->get();

        return view('owner.products.index', compact('products'));
    }

    /**
     * 新規商品作成フォーム
     */
    public function create()
    {
        return view('owner.products.create');
    }

    /**
     * 新規商品保存
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active'   => 'required|boolean',
            'image'       => 'nullable|image|max:4096', // 4MB
        ]);

        $imageName = null;

        // 画像保存
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->storeAs('public/products', $imageName);
        }

        Product::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'description' => $request->description,
            'is_active'   => $request->is_active,
            'image'       => $imageName,
        ]);

        return redirect()->route('owner.products.index')
            ->with('status', '商品を追加しました！');
    }

    /**
     * 編集フォーム
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('owner.products.edit', compact('product'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active'   => 'required|boolean',
            'image'       => 'nullable|image|max:4096',
        ]);

        $product = Product::findOrFail($id);

        $imageName = $product->image;

        // 新しい画像がアップされた場合
        if ($request->hasFile('image')) {
            // 古い画像削除
            if ($imageName && Storage::exists('public/products/' . $imageName)) {
                Storage::delete('public/products/' . $imageName);
            }

            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->storeAs('public/products', $imageName);
        }

        $product->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'description' => $request->description,
            'is_active'   => $request->is_active,
            'image'       => $imageName,
        ]);

        return redirect()->route('owner.products.index')
            ->with('status', '商品情報を更新しました！');
    }

    /**
     * 削除処理
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // 画像削除
        if ($product->image && Storage::exists('public/products/' . $product->image)) {
            Storage::delete('public/products/' . $product->image);
        }

        $product->delete();

        return redirect()->route('owner.products.index')
            ->with('status', '商品を削除しました！');
    }
}
