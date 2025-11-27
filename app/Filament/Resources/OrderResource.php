<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;   // ← ✅ 修正ポイント
use Filament\Tables;
use Filament\Tables\Table; // ← ✅ 修正ポイント
use Filament\Resources\Resource;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = '注文管理';
    protected static ?string $pluralModelLabel = '注文';
    protected static ?string $modelLabel = '注文';
    protected static ?string $navigationGroup = '販売管理';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('order_number')->label('注文番号')->required()->readOnly(),
            Forms\Components\TextInput::make('customer_name')->label('氏名')->required(),
            Forms\Components\TextInput::make('customer_email')->label('メールアドレス')->required(),
            Forms\Components\Textarea::make('shipping_address')->label('住所')->rows(3),
            Forms\Components\Select::make('status')
                ->label('ステータス')
                ->options([
                    'pending' => '入金待ち',
                    'paid' => '入金確認',
                    'shipped' => '発送済み',
                    'refunded' => '返金済み',
                ]),
            Forms\Components\TextInput::make('tracking_number')->label('送り状番号'),
            Forms\Components\DateTimePicker::make('shipped_at')->label('発送日時'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('注文番号')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('customer_name')->label('購入者')->searchable(),
                Tables\Columns\TextColumn::make('total_amount')->label('合計金額')->money('JPY', true),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('ステータス')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'paid',
                        'success' => 'shipped',
                        'danger' => 'refunded',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => '入金待ち',
                        'paid' => '入金確認',
                        'shipped' => '発送済み',
                        'refunded' => '返金済み',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('updated_at')->label('更新日')->dateTime('Y-m-d H:i'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
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
