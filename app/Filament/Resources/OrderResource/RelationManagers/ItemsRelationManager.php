<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = '注文商品';

    protected static ?string $modelLabel = '注文商品';

    protected static ?string $pluralModelLabel = '注文商品';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')

            ->columns([

                Tables\Columns\ImageColumn::make('product.main_image_url')
                    ->label('画像')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl(asset('images/no-image.png')),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('商品名')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('member_name')
                    ->label('メンバー')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('variant.name')
                    ->label('バリエーション')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('単価')
                    ->money('JPY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('数量')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('小計')
                    ->money('JPY')
                    ->sortable(),

            ])

            ->headerActions([])

            ->actions([])

            ->bulkActions([]);
    }
}
