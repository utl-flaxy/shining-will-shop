<?php

namespace App\Filament\AdminShining\Resources;

use App\Models\Product;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ExportAction;
use App\Filament\AdminShining\Resources\ProductResource\Pages;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $label = '商品';
    protected static ?string $pluralLabel = '商品一覧';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('商品名')->required(),
            Forms\Components\Textarea::make('description')->label('説明文'),
            Forms\Components\TextInput::make('price')->numeric()->required()->label('価格'),
            Forms\Components\FileUpload::make('image')->label('商品画像')->image(),
            Forms\Components\Toggle::make('stock_enabled')->label('在庫管理有効'),
            Forms\Components\DateTimePicker::make('start_at')->label('販売開始'),
            Forms\Components\DateTimePicker::make('end_at')->label('販売終了'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\ImageColumn::make('image')->label('画像'),
            Tables\Columns\TextColumn::make('name')->searchable()->label('商品名'),
            Tables\Columns\TextColumn::make('price')->label('価格')->money('JPY', true),
            Tables\Columns\IconColumn::make('stock_enabled')->boolean()->label('在庫管理'),
            Tables\Columns\TextColumn::make('start_at')->dateTime()->label('販売開始'),
            Tables\Columns\TextColumn::make('end_at')->dateTime()->label('販売終了'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
            ExportAction::make()->label('Excel出力'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
