<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Mail\OrderShippedMail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon  = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = '注文管理';
    protected static ?string $navigationGroup = '販売管理';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('注文番号'),
                Tables\Columns\TextColumn::make('customer_name')->label('購入者'),
                Tables\Columns\TextColumn::make('total_amount')->label('合計金額'),
                Tables\Columns\TextColumn::make('payment_method')->label('支払い方法'),
                Tables\Columns\TextColumn::make('payment_status')->label('支払い状態'),
                Tables\Columns\TextColumn::make('delivery_method')->label('配送方法'),
                Tables\Columns\TextColumn::make('status')->label('注文状態'),
                Tables\Columns\TextColumn::make('created_at')->label('注文日時')->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }
}
