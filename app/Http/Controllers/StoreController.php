<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    /**
     * 🏠 トップページ（カテゴリ一覧 + 新着商品）
     */
    public function index()
    {
        try {
            // カテゴリ一覧（名前順）
            $categories = Category::orderBy('name')->get();

            // 新着商品（作成日時の降順で8件）
            $newProducts = Product::orderBy('created_at', 'desc')->take(8)->get();

            // ✅ ビューへ安全にデータを渡す
            return view('shop.index', compact('categories', 'newProducts'));
        } catch (\Throwable $e) {
            Log::error('❌ StoreController@index エラー: ' . $e->getMessage());

            // 万が一エラーがあってもトップページは壊さない
            return view('shop.index', [
                'categories' => collect(),
                'newProducts' => collect(),
            ]);
        }
    }

    /**
     * 🗂 カテゴリ別商品一覧
     */
    public function category($id)
    {
        try {
            $category = Category::findOrFail($id);
            $products = Product::where('category_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return view('shop.category', compact('category', 'products'));
        } catch (\Throwable $e) {
            Log::error("❌ StoreController@category エラー: " . $e->getMessage());

            // カテゴリが存在しない or エラー時に安全にリダイレクト
            return redirect()->route('store.index')->with('error', '指定されたカテゴリが見つかりません。');
        }
    }
}
