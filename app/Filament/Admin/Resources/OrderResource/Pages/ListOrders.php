<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_csv')
                ->label('CSV出力')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('filament.orders.export.csv')) // ✅ ここが重要
                ->openUrlInNewTab(),
        ];
    }
}
