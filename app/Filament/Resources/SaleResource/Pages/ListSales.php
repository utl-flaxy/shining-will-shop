<?php

namespace App\Filament\Resources\SaleResource\Pages;

use Filament\Pages\Page;
use App\Services\StripeService;

class ListSales extends Page
{
    protected static string $resource = \App\Filament\Resources\SaleResource::class;

    protected static string $view = 'filament.pages.list-sales';

    protected static ?string $title = '売上一覧';

    public $sales;

    public function mount()
    {
        $stripe = app(StripeService::class);
        $sales = $stripe->getRecentSales();

        $this->sales = collect($sales->data)->map(function ($sale) {
            return (object)[
                'id' => $sale->id,
                'amount' => $sale->amount / 100,
                'status' => $sale->status,
                'created' => date('Y-m-d H:i', $sale->created),
            ];
        });
    }
}
