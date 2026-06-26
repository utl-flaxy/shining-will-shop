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

    protected static ?string $navigationIcon  = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = '注文管理';
    protected static ?string $navigationGroup = '販売管理';

    /* ============================
        フォーム（詳細・編集）
    ============================ */
    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── 注文金額情報 ─────────────────
            Forms\Components\Section::make('注文情報')
                ->schema([
                    Forms\Components\TextInput::make('order_number')
                        ->label('注文番号')
                        ->disabled(),

                    Forms\Components\TextInput::make('subtotal')
                        ->label('小計')
                        ->numeric()
                        ->disabled(),

                    Forms\Components\TextInput::make('shipping_fee')
                        ->label('送料')
                        ->numeric()
                        ->disabled(),

                    Forms\Components\TextInput::make('total_amount')
                        ->label('合計金額')
                        ->numeric()
                        ->disabled(),
                ])
                ->columns(4),

            // ── 購入者情報 ─────────────────
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
                        ->label('住所')
                        ->rows(2)
                        ->disabled(),
                ])
                ->columns(2),

            // ── 配送・決済状態（基本は自動更新） ─────────────────
            Forms\Components\Section::make('配送・決済')
                ->schema([
                    Forms\Components\Select::make('delivery_method')
                        ->label('配送方法')
                        ->options([
                            'sagawa' => '佐川',
                            'pickup' => '現地渡し',
                        ])
                        ->disabled(),

                    Forms\Components\Select::make('payment_method')
                        ->label('支払い方法')
                        ->options([
                            'card'          => 'カード',
                            'bank_transfer' => '口座振込',
                            'on_site'       => '現地払い',
                        ])
                        ->disabled(),

                    Forms\Components\Select::make('payment_status')
                        ->label('支払い状態')
                        ->options([
                            'unpaid'   => '未入金',
                            'paid'     => '入金済み',
                            'refunded' => '返金済み',
                        ])
                        ->disabled(),

                    Forms\Components\Select::make('status')
                        ->label('注文状態')
                        ->options([
                            'pending'  => '入金待ち',
                            'paid'     => '入金確認',
                            'shipped'  => '発送済み',
                            'refunded' => '返金済み',
                        ])
                        ->disabled(),
                ])
                ->columns(4),

            // ── 発送・返金（ここは管理画面から更新） ─────────────────
            Forms\Components\Section::make('発送・返金')
                ->schema([
                    Forms\Components\TextInput::make('tracking_number')
                        ->label('送り状番号'),

                    Forms\Components\DateTimePicker::make('paid_at')
                        ->label('支払日時'),

                    Forms\Components\DateTimePicker::make('shipped_at')
                        ->label('発送日時'),

                    Forms\Components\TextInput::make('refunded_amount')
                        ->label('返金額')
                        ->numeric(),

                    Forms\Components\DateTimePicker::make('refunded_at')
                        ->label('返金日時'),

                    Forms\Components\Textarea::make('refund_reason')
                        ->label('返金理由'),
                ])
                ->columns(3),
        ]);
    }

    /* ============================
        一覧テーブル
    ============================ */
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
                    ->formatStateUsing(fn($state) => match ($state) {
                        'card'          => 'クレジットカード',
                        'bank_transfer' => '口座振込',
                        'on_site'       => '現地払い',
                        default         => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('支払い状態')
                    ->badge()
                    ->colors([
                        'gray'    => 'unpaid',
                        'success' => 'paid',
                        'danger'  => 'refunded',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'unpaid'   => '未入金',
                        'paid'     => '入金済み',
                        'refunded' => '返金済み',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('delivery_method')
                    ->label('配送方法')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'sagawa' => '佐川配送',
                        'pickup' => '現場渡し',
                        default  => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('注文状態')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'paid',
                        'success' => 'shipped',
                        'danger'  => 'refunded',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending'  => '入金待ち',
                        'paid'     => '入金確認',
                        'shipped'  => '発送済み',
                        'refunded' => '返金済み',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('注文日時')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('注文状態')
                    ->options([
                        'pending'  => '入金待ち',
                        'paid'     => '入金確認',
                        'shipped'  => '発送済み',
                        'refunded' => '返金済み',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('支払い方法')
                    ->options([
                        'bank_transfer' => '口座振込',
                        'card'          => 'クレジットカード',
                        'on_site'       => '現地払い',
                    ]),
            ])

            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('詳細'),

                Tables\Actions\EditAction::make()
                    ->label('編集'),

                // ✅ 入金確認ボタン
                Tables\Actions\Action::make('confirm_deposit')
                    ->label('入金確認')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Order $record) =>
                        $record->payment_status === 'unpaid'
                    )
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update([
                            'payment_status'             => 'paid',
                            'paid_at'                    => now(),
                            'bank_deposit_confirmed_at'  => now(),
                            'status'                     => 'paid',
                        ]);
                    }),

                // ✅ 発送完了 & メール送信ボタン
                Tables\Actions\Action::make('mark_as_shipped')
                    ->label('発送完了メール送信')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Order $record) =>
                        $record->status === 'paid' &&
                        $record->shipped_at === null
                    )
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('送り状番号')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {

                        // DB 更新
                        $record->update([
                            'tracking_number' => $data['tracking_number'],
                            'shipped_at'      => now(),
                            'status'          => 'shipped',
                        ]);

                        // 発送完了メール送信
                        Mail::to($record->customer_email)
                            ->send(new OrderShippedMail($record));
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
