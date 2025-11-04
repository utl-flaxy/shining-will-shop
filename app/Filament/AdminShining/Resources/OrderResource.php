<?php

namespace App\Filament\AdminShining\Resources;

use App\Models\Order;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\Action;
use App\Filament\AdminShining\Resources\OrderResource\Pages;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?string $label = '注文';
    protected static ?string $pluralLabel = '注文一覧';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')->label('購入者')->required(),
            Forms\Components\Select::make('payment_method')
                ->options([
                    'card' => 'クレジットカード',
                    'bank_transfer' => '銀行振込',
                    'on_site' => '現場払い',
                ])
                ->label('支払方法')
                ->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending_payment' => '入金待ち',
                    'paid' => '入金済',
                    'awaiting_shipment' => '発送待ち',
                    'shipped' => '発送完了',
                    'refunded' => '返金済',
                    'cancelled' => 'キャンセル',
                ])
                ->label('ステータス')
                ->required(),
            Forms\Components\TextInput::make('total_amount')->numeric()->label('合計金額'),
            Forms\Components\Textarea::make('memo')->label('備考'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('購入者'),
                Tables\Columns\TextColumn::make('total_amount')->label('合計金額')->money('JPY', true),
                Tables\Columns\BadgeColumn::make('status')->label('ステータス')
                    ->colors([
                        'warning' => 'pending_payment',
                        'info' => 'paid',
                        'primary' => 'awaiting_shipment',
                        'success' => 'shipped',
                        'danger' => 'refunded',
                        'gray' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('payment_method')->label('支払方法'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('注文日時'),
            ])
            ->actions([
                Action::make('markPaid')->label('入金確認')->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'pending_payment')
                    ->action(fn($record) => $record->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ])),
                Action::make('markShipped')->label('発送完了')->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'awaiting_shipment')
                    ->action(fn($record) => $record->update([
                        'status' => 'shipped',
                        'shipped_at' => now(),
                    ])),
                Action::make('refund')->label('返金')->color('danger')->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'awaiting_shipment')
                    ->action(function ($record) {
                        // Stripe Refund API呼び出しなどをここに実装
                        $record->update([
                            'status' => 'refunded',
                            'refunded_at' => now(),
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportAction::make()->label('注文Excel出力'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
