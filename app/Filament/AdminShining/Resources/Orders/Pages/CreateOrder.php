<?php

namespace App\Filament\AdminShining\Resources\Orders\Pages;

use App\Filament\AdminShining\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
