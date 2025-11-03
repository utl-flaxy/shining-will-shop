<?php

namespace App\Filament\AdminShining\Resources\Orders;

use App\Models\Order;
use App\Filament\AdminShining\Resources\Orders\Pages as Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;                 // v4: Schema ベース
use Filament\Tables\Table;                   // v4: Table ベース
use Filament\Tables\Columns\TextColumn;
use BackedEnum;                              // v4: navigationIcon 等で Enum 許容

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    // 親シグネチャに合わせて BackedEnum|string|null を許容
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = '注文一覧';
    // navigationGroup は未指定（必要なら UnitEnum|string|null で再追加可）

    // v4: Schema ベースの form シグネチャ
    public static function form(Schema $schema): Schema
    {
        // まずは空でOK（必要に応じて項目追加）
        return $schema->schema([]);
    }

    // v4: Table ベースの table シグネチャ
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 必要に応じて作成:
            // 'view'  => Pages\ViewOrder::route('/{record}'),
            // 'create'=> Pages\CreateOrder::route('/create'),
            // 'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
