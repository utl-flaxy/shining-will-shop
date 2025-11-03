<?php

namespace App\Filament\AdminShining\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            FileUpload::make('images')
                ->label('商品画像 (複数可)')
                ->image()
                ->multiple()
                ->directory('products')
                ->disk('public'),

            TextInput::make('name')->label('商品名'),
            TextInput::make('price')->label('価格')->numeric(),
        ]);
    }
}
