<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class StoreController extends Controller
{
    /**
     * ✅ トップページ
     */
    public function home()
    {
        // ✅ 並び順は name でOK（orderカラムは存在しない）
        $categories = Category::orderBy('name')->get();

        $newProducts = Product::with('images')
            ->where('is_published', 1)
            ->where('is_active', 1)
            ->latest()
            ->take(8)
            ->get();

        return view('shop.home', compact('categories', 'newProducts'));
    }

    /**
     * ✅ 商品一覧ページ
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with('images')
            ->where('is_published', 1)
            ->where('is_active', 1)
            ->latest()
            ->paginate(12);

        return view('shop.index', compact('categories', 'products'));
    }

    /**
     * ✅ カテゴリ別商品一覧
     */
    public function category($id)
    {
        $category = Category::findOrFail($id);

        $products = Product::with('images')
            ->where('category_id', $id)
            ->where('is_active', 1)
            ->latest()
            ->paginate(12);

        return view('shop.category', compact('category', 'products'));
    }
}
