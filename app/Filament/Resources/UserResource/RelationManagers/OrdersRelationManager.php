<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = '注文履歴';

    protected static ?string $modelLabel = '注文';

    protected static ?string $pluralModelLabel = '注文';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_number')

            ->columns([

                Tables\Columns\TextColumn::make('order_number')
                    ->label('注文番号')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('合計金額')
                    ->money('JPY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method_label')
                    ->label('支払い方法'),

                Tables\Columns\TextColumn::make('payment_status_label')
                    ->label('決済'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('発送状況')
                    ->formatStateUsing(fn ($record) => $record->status_label)
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'preparing',
                        'info' => 'shipped',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('注文日')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

            ])

            ->headerActions([

            ])

            ->actions([

                Tables\Actions\Action::make('open')

                    ->label('詳細')

                    ->icon('heroicon-o-eye')

                    ->url(fn ($record) => route(
                        'filament.admin.resources.orders.edit',
                        [
                            'record' => $record,
                        ]
                    )),

            ])

            ->bulkActions([

            ])

            ->defaultSort(
                'created_at',
                'desc'
            );
    }
}
