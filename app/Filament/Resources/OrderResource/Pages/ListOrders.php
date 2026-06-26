<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

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

                // ✅ モーダルフォーム
                ->form([
                    DatePicker::make('start_date')
                        ->label('開始日'),

                    DatePicker::make('end_date')
                        ->label('終了日'),

                    TextInput::make('product_name')
                        ->label('商品名（部分一致）'),
                ])

                // ✅ フォーム送信後にクエリ付きでCSV出力
                ->action(function (array $data) {
                    $query = http_build_query([
                        'start_date'  => $data['start_date'] ?? null,
                        'end_date'    => $data['end_date'] ?? null,
                        'product_name' => $data['product_name'] ?? null,
                    ]);

                    $url = '/admin/orders/export/csv?' . $query;

                    redirect($url);
                }),
        ];
    }
}
