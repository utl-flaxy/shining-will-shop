<?php

namespace App\Filament\AdminShining\Resources\Orders\Pages;

use App\Filament\AdminShining\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
