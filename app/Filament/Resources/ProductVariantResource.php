<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'メンバー別在庫';
    protected static ?string $navigationGroup = '在庫管理';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')
                ->required()
                ->label('商品'),

            Forms\Components\TextInput::make('member_name')
                ->required()
                ->label('メンバー名'),

            Forms\Components\TextInput::make('stock')
                ->numeric()
                ->required()
                ->minValue(0)
                ->label('在庫数'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('商品'),
                Tables\Columns\TextColumn::make('member_name')->label('メンバー'),
                Tables\Columns\TextColumn::make('stock')->label('在庫'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit'   => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
