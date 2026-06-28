<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        // 非公開・無効商品のみ404
        if (! $product->is_active || ! $product->is_published) {
            abort(404);
        }

        $product->load(['variants', 'images']);

        return view('products.show', compact('product'));
    }
}
