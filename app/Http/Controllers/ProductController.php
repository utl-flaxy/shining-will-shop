<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // ---------------------------------------------------
    // 🏠 トップページ（商品一覧）
    // ---------------------------------------------------
    public function index()
    {
        $products = Product::latest()->take(12)->get(); // 新着順
        $categories = Category::all(); // カテゴリカード表示用

        return view('shop.index', compact('products', 'categories'));
    }

    // ---------------------------------------------------
    // 📦 商品詳細ページ
    // ---------------------------------------------------
    public function show(Product $product)
    {
        return view('shop.show', compact('product'));
    }
}
