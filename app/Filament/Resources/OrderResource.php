<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = '注文管理';

    protected static ?string $navigationGroup = '販売管理';

    /*
    |--------------------------------------------------------------------------
    | フォーム
    |--------------------------------------------------------------------------
    */

    public static function form(Form $form): Form
    {
        return $form->schema([

            /*
            |--------------------------------------------------------------------------
            | 注文情報
            |--------------------------------------------------------------------------
            */

            Forms\Components\Section::make('注文情報')
                ->schema([

                    Forms\Components\TextInput::make('order_number')
                        ->label('注文番号')
                        ->disabled(),

                    Forms\Components\TextInput::make('subtotal')
                        ->label('商品合計')
                        ->numeric()
                        ->disabled(),

                    Forms\Components\TextInput::make('shipping_fee')
                        ->label('送料')
                        ->numeric()
                        ->disabled(),

                    Forms\Components\TextInput::make('total_amount')
                        ->label('請求金額')
                        ->numeric()
                        ->disabled(),

                ])
                ->columns(4),

            /*
            |--------------------------------------------------------------------------
            | 購入者情報
            |--------------------------------------------------------------------------
            */

            Forms\Components\Section::make('購入者情報')
                ->schema([

                    Forms\Components\TextInput::make('customer_name')
                        ->label('氏名')
                        ->disabled(),

                    Forms\Components\TextInput::make('customer_email')
                        ->label('メールアドレス')
                        ->disabled(),

                    Forms\Components\TextInput::make('customer_phone')
                        ->label('電話番号')
                        ->disabled(),

                    Forms\Components\Textarea::make('shipping_address')
                        ->label('配送先住所')
                        ->rows(2)
                        ->disabled(),

                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | 配送・決済
            |--------------------------------------------------------------------------
            */

            Forms\Components\Section::make('配送・決済')
                ->schema([

                    Forms\Components\Select::make('delivery_method')
                        ->label('配送方法')
                        ->options([
                            'sagawa' => '佐川配送',
                            'pickup' => '現地渡し',
                        ])
                        ->disabled(),

                    Forms\Components\Select::make('payment_method')
                        ->label('支払い方法')
                        ->options([
                            'card' => 'クレジットカード',
                            'bank_transfer' => '銀行振込',
                            'on_site' => '現地払い',
                        ])
                        ->disabled(),

                    Forms\Components\Select::make('payment_status')
                        ->label('決済状態')
                        ->options([
                            'unpaid' => '未入金',
                            'paid' => '入金済み',
                            'refunded' => '返金済み',
                            'failed' => '決済失敗',
                        ])
                        ->disabled(),

                    Forms\Components\Select::make('status')
                        ->label('注文ステータス')
                        ->options(Order::statuses())
                        ->disabled(),
                                            Forms\Components\Section::make('発送管理')
                        ->schema([

                            Forms\Components\TextInput::make('tracking_number')
                                ->label('送り状番号')
                                ->maxLength(255),

                            Forms\Components\DateTimePicker::make('paid_at')
                                ->label('入金日時')
                                ->disabled(),

                            Forms\Components\DateTimePicker::make('bank_deposit_confirmed_at')
                                ->label('入金確認日時')
                                ->disabled(),

                            Forms\Components\DateTimePicker::make('shipped_at')
                                ->label('発送日時')
                                ->disabled(),

                        ])
                        ->columns(2),

                    /*
                    |--------------------------------------------------------------------------
                    | 返金情報
                    |--------------------------------------------------------------------------
                    */

                    Forms\Components\Section::make('返金情報')
                        ->collapsed()
                        ->schema([

                            Forms\Components\TextInput::make('refunded_amount')
                                ->label('返金額')
                                ->numeric(),

                            Forms\Components\DateTimePicker::make('refunded_at')
                                ->label('返金日時'),

                            Forms\Components\Textarea::make('refund_reason')
                                ->label('返金理由')
                                ->rows(3),

                        ])
                        ->columns(3),

                ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 一覧テーブル
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                                Tables\Columns\TextColumn::make('order_number')
                    ->label('注文番号')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('購入者')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('合計金額')
                    ->money('JPY', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('支払い方法')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'card' => 'クレジットカード',
                        'bank_transfer' => '銀行振込',
                        'on_site' => '現地払い',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('決済状態')
                    ->badge()
                    ->colors([
                        'gray' => 'unpaid',
                        'success' => 'paid',
                        'danger' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'unpaid' => '未入金',
                        'paid' => '入金済み',
                        'refunded' => '返金済み',
                        'failed' => '決済失敗',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('delivery_method')
                    ->label('配送方法')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'sagawa' => '佐川配送',
                        'pickup' => '現地渡し',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('注文ステータス')
                    ->badge()
                    ->colors([
                        'warning' => Order::STATUS_PENDING,
                        'info' => Order::STATUS_PREPARING,
                        'primary' => Order::STATUS_SHIPPED,
                        'success' => Order::STATUS_COMPLETED,
                        'danger' => Order::STATUS_CANCELLED,
                    ])
                    ->formatStateUsing(fn ($state) => Order::statuses()[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('注文日時')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make('status')
                    ->label('注文ステータス')
                    ->options(Order::statuses()),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('支払い方法')
                    ->options([
                        'bank_transfer' => '銀行振込',
                        'card' => 'クレジットカード',
                        'on_site' => '現地払い',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('決済状態')
                    ->options([
                        'unpaid' => '未入金',
                        'paid' => '入金済み',
                        'refunded' => '返金済み',
                        'failed' => '決済失敗',
                    ]),
            ])

            ->actions([
                                // ============================
                // 入金確認
                // ============================

                Tables\Actions\Action::make('confirm_payment')
                    ->label('入金確認')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Order $record) =>
                        $record->payment_status === 'unpaid'
                    )
                    ->requiresConfirmation()
                    ->action(function (Order $record) {

                        $record->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                            'bank_deposit_confirmed_at' => now(),
                        ]);

                    }),

                // ============================
                // 発送準備開始
                // ============================

                Tables\Actions\Action::make('start_shipping')
                    ->label('発送準備開始')
                    ->icon('heroicon-o-cube')
                    ->color('warning')
                    ->visible(fn (Order $record) =>
                        $record->payment_status === 'paid'
                        && $record->status === Order::STATUS_PENDING
                    )
                    ->requiresConfirmation()
                    ->action(function (Order $record) {

                        $record->update([
                            'status' => Order::STATUS_PREPARING,
                        ]);

                    }),

                // ============================
                // 発送完了
                // ============================

                Tables\Actions\Action::make('mark_as_shipped')
                    ->label('発送完了')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Order $record) =>
                        $record->status === Order::STATUS_PREPARING
                    )
                    ->requiresConfirmation()
                    ->form([

                        Forms\Components\TextInput::make('tracking_number')
                            ->label('送り状番号')
                            ->required(),

                    ])
                    ->action(function (Order $record, array $data) {

                        $record->update([

                            'tracking_number' => $data['tracking_number'],

                            'shipped_at' => now(),

                            'status' => Order::STATUS_SHIPPED,

                        ]);

                        Mail::to($record->customer_email)
                            ->send(new OrderShippedMail($record));

                    }),

                // ============================
                // 配送完了
                // ============================

                Tables\Actions\Action::make('complete')
                    ->label('配送完了')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) =>
                        $record->status === Order::STATUS_SHIPPED
                    )
                    ->requiresConfirmation()
                    ->action(function (Order $record) {

                        $record->update([
                            'status' => Order::STATUS_COMPLETED,
                        ]);

                    }),

                Tables\Actions\ViewAction::make()
                    ->label('詳細'),

                Tables\Actions\EditAction::make()
                    ->label('編集'),

            ])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]));
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
