<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'メンバー別在庫';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('member_name')
                ->label('メンバー名')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('stock')
                ->label('在庫数')
                ->numeric()
                ->required()
                ->minValue(0),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member_name')
                    ->label('メンバー')
                    ->searchable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('在庫')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('在庫を追加'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'asc');
    }
}
