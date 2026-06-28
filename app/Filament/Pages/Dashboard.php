<?php

namespace App\Filament\Pages;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $panel = 'admin_shining';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'ダッシュボード';

    protected static ?string $title = 'ダッシュボード';

    protected static string $view = 'filament.pages.dashboard';

    public int $totalSales = 0;

    public int $todaySales = 0;

    public int $monthlySales = 0;

    public int $orderCount = 0;

    public int $productCount = 0;

    public int $userCount = 0;

    public int $categoryCount = 0;

    public int $soldOutCount = 0;

    public int $shippingWaitingCount = 0;

    public $latestOrders;

    public function mount(): void
    {
        // 売上集計
        $this->totalSales = (int) Order::sum('total_amount');

        $this->todaySales = (int) Order::whereDate(
            'created_at',
            today()
        )->sum('total_amount');

        $this->monthlySales = (int) Order::whereYear(
            'created_at',
            now()->year
        )->whereMonth(
            'created_at',
            now()->month
        )->sum('total_amount');

        // 件数集計
        $this->orderCount = Order::count();

        $this->productCount = Product::count();

        $this->userCount = User::count();

        $this->categoryCount = Category::count();

        // 売り切れ商品数
        $this->soldOutCount = Product::all()
            ->filter(fn (Product $product) => $product->totalStock() <= 0)
            ->count();

        // 発送待ち件数
        $this->shippingWaitingCount = Order::whereIn(
            'status',
            [
                OrderStatus::Pending->value,
                OrderStatus::Preparing->value,
            ]
        )->count();

        // 最新注文
        $this->latestOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();
    }
}
