<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;

class SalesPage extends Page
{
    protected static ?string $panel = 'admin_shining';

    protected static ?string $navigationIcon = 'heroicon-o-currency-yen';

    protected static ?string $navigationLabel = '売上一覧';

    protected static ?string $title = '売上一覧';

    protected static string $view = 'filament.pages.sales-page';

    public int $totalSales = 0;

    public int $todaySales = 0;

    public int $monthlySales = 0;

    public int $totalOrders = 0;

    public $recentOrders = [];

    public function mount(): void
    {
        $query = Order::query();

        $this->totalSales = (int) $query->sum('total_amount');

        $this->totalOrders = (int) $query->count();

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

        $this->recentOrders = Order::latest()
            ->take(10)
            ->get();
    }
}
