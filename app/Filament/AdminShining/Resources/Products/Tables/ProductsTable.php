<?php

namespace App\Filament\AdminShining\Resources\Products\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Arr;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ✅ サムネイル列
                ImageColumn::make('images.0')
                    ->label('画像')
                    ->square()
                    ->getStateUsing(function ($record) {
                        // images が配列なら最初の要素、単一文字列ならそのまま
                        $images = $record->images ?? null;
                        if (is_array($images)) {
                            return Arr::get($images, 0);
                        } elseif (is_string($images)) {
                            return $images;
                        }
                        // 古い image カラムがあればフォールバック
                        return $record->image ?? null;
                    })
                    ->disk('public')
                    ->visibility('public')
                    ->height(60),

                TextColumn::make('title')
                    ->label('商品名')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('価格')
                    ->money('JPY', true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('登録日')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ]);
    }
}
