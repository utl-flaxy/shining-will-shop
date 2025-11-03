<?php

namespace App\Filament\AdminShining\Resources\Orders;

use App\Models\Order;
use App\Filament\AdminShining\Resources\Orders\Pages as Pages;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use UnitEnum;
use BackedEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = '注文一覧';

    // Filament が期待する Form 型に合わせる
    public static function form(Form $form): Form
    {
        return $form->schema([
            // 必要に応じてフィールドを追加してください
            TextInput::make('status')->label('状態'),
            TextInput::make('total_amount')->label('合計'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('注文ID')->sortable(),
                TextColumn::make('status')->label('状態')->badge(),
                TextColumn::make('total_amount')->label('合計')->money('JPY', true),
                TextColumn::make('created_at')->label('作成日')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
