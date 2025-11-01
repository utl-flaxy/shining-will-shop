<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->where('is_active', true)->latest()->paginate(12);
        return view('front.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        return view('front.products.show', compact('product'));
    }
}
