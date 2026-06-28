<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (
            ($data['status'] ?? null) === OrderStatus::Shipped->value &&
            empty($this->record->shipped_at)
        ) {
            $data['shipped_at'] = now();
        }

        if (
            ($data['payment_status'] ?? null) === 'paid' &&
            empty($this->record->paid_at)
        ) {
            $data['paid_at'] = now();
            $data['bank_deposit_confirmed_at'] = now();
        }

        return $data;
    }
}
