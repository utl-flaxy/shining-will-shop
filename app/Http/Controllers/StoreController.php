<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | トップページ
    |--------------------------------------------------------------------------
    */

    public function home()
    {
        $categories = Category::orderBy('name')->get();

        $newProducts = Product::with([
                'images',
                'category',
            ])
            ->published()
            ->latest()
            ->take(8)
            ->get();

        return view('shop.home', compact(
            'categories',
            'newProducts'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | 商品一覧
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 検索条件
        |--------------------------------------------------------------------------
        */

        // PHPUnit対策
        $keyword = $request->input('keyword')
            ?? $request->input('search');

        $category = $request->input('category');

        $sort = $request->input('sort');

        $minPrice = $request->input('min_price');

        $maxPrice = $request->input('max_price');

        /*
        |--------------------------------------------------------------------------
        | カテゴリ一覧
        |--------------------------------------------------------------------------
        */

        $categories = Category::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | 商品一覧
        |--------------------------------------------------------------------------
        */

        $products = Product::query()

            ->with([
                'images',
                'category',
            ])

            ->published()

            ->keyword($keyword)

            ->category($category)

            ->when(
                filled($minPrice),
                fn ($query) => $query->where('price', '>=', $minPrice)
            )

            ->when(
                filled($maxPrice),
                fn ($query) => $query->where('price', '<=', $maxPrice)
            )

            ->sort($sort)

            ->paginate(12)

            ->withQueryString();

        return view(
            'shop.index',
            compact(
                'products',
                'categories',
                'keyword',
                'category',
                'sort',
                'minPrice',
                'maxPrice'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | カテゴリ一覧
    |--------------------------------------------------------------------------
    */

    public function category(
        Request $request,
        Category $category
    ) {
        $sort = $request->input('sort');

        $products = Product::query()

            ->with([
                'images',
                'category',
            ])

            ->published()

            ->where(
                'category_id',
                $category->id
            )

            ->sort($sort)

            ->paginate(12)

            ->withQueryString();

        return view(
            'shop.category',
            [
                'category' => $category,
                'products' => $products,
                'sort' => $sort,
            ]
        );
    }
}
