<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Columns\ImageColumn;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('画像')->square()->height(60),
                TextColumn::make('name')->label('商品名')->searchable(),
                TextColumn::make('price')->label('価格')->money('JPY'),
                TextColumn::make('stock')->label('在庫'),
                IconColumn::make('is_active')->label('販売状態')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
