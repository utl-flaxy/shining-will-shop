<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Order;
use Carbon\Carbon;

class SalesSummary extends Widget
{
    protected static string $view = 'filament.widgets.sales-summary';
    protected array|string|int $columnSpan = 2;

    public function getData(): array
    {
        $todayDate = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $today = (int) Order::whereDate('created_at', $todayDate)->sum('total_amount');
        $month = (int) Order::whereBetween('created_at', [$startOfMonth, Carbon::now()])->sum('total_amount');

        $last7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $sum = (int) Order::whereDate('created_at', $day)->sum('total_amount');
            $last7[$day->format('m/d')] = $sum;
        }

        return [
            'today' => $today,
            'month' => $month,
            'last7' => $last7,
        ];
    }
}
