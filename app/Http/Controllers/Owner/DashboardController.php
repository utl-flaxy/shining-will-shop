<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $recentOrders  = Order::latest()->take(5)->get();

        return view('owner.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'recentOrders',
        ));
    }
}
