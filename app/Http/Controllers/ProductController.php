<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        if (! $product->isAvailableForSale()) {
            abort(404);
        }

        $product->load(['variants', 'images']);

        return view('products.show', compact('product'));
    }
}
