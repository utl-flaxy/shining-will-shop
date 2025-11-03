<?php

namespace App\Filament\AdminShining\Resources\Orders\Pages;

use App\Filament\AdminShining\Resources\Orders\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
}
