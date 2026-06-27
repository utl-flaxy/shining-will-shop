<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->active();

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Apply price range filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->priceRange(
                $request->filled('min_price') ? (int)$request->min_price : null,
                $request->filled('max_price') ? (int)$request->max_price : null
            );
        }

        // Get all categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        // Paginate results
        $products = $query->latest()->paginate(12);
        
        return view('front.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        return view('front.products.show', compact('product'));
    }
}
