<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Carbon\Carbon;

class SalesStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', 'shipped')
            ->sum('total_amount');

        $monthSales = Order::whereBetween('created_at', [$monthStart, now()])
            ->where('status', 'shipped')
            ->sum('total_amount');

        $monthOrders = Order::whereBetween('created_at', [$monthStart, now()])
            ->where('status', 'shipped')
            ->count();

        $average = $monthOrders > 0
            ? round($monthSales / $monthOrders)
            : 0;

        return [
            Stat::make('今日の売上', '¥' . number_format($todaySales)),
            Stat::make('今月の売上', '¥' . number_format($monthSales)),
            Stat::make('今月の注文数', $monthOrders . ' 件'),
            Stat::make('平均単価', '¥' . number_format($average)),
        ];
    }
}
