<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * 商品一覧
     */
    public function index()
    {
        // カテゴリも一緒に取得（一覧表示用）
        $products = Product::with('category')
            ->latest()
            ->get();

        return view('owner.products.index', compact('products'));
    }

    /**
     * 新規商品作成フォーム
     */
    public function create()
    {
        // ✅ カテゴリ一覧を取得（create.blade.php 用）
        $categories = Category::orderBy('name')->get();

        return view('owner.products.create', compact('categories'));
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
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|max:4096', // 4MB
        ]);

        $imageName = null;

        // 画像保存
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->storeAs('public/products', $imageName);
        }

        Product::create([
            'name'         => $request->name,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'description'  => $request->description,
            'is_active'    => $request->is_active,
            'category_id'  => $request->category_id,
            'image'        => $imageName,
        ]);

        return redirect()
            ->route('owner.products.index')
            ->with('status', '商品を追加しました！');
    }

    /**
     * 編集フォーム（A-13）
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        // ✅ カテゴリ一覧（edit.blade.php 用）
        $categories = Category::orderBy('name')->get();

        return view('owner.products.edit', compact('product', 'categories'));
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
            'category_id' => 'nullable|exists:categories,id',
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
            'name'         => $request->name,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'description'  => $request->description,
            'is_active'    => $request->is_active,
            'category_id'  => $request->category_id,
            'image'        => $imageName,
        ]);

        return redirect()
            ->route('owner.products.index')
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

        return redirect()
            ->route('owner.products.index')
            ->with('status', '商品を削除しました！');
    }
}
