<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\SalesStats;
use App\Filament\Admin\Widgets\MonthlySalesChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            SalesStats::class, // 本日・今月の売上
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            MonthlySalesChart::class, // 月別売上グラフ
        ];
    }
}
