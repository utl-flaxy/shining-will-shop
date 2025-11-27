<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * 一覧の説明文（任意）
     */
    public function getSubheading(): ?string
    {
        return '登録されているカテゴリーの一覧です。';
    }
}
