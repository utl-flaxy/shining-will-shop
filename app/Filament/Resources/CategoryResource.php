<?php

namespace App\Filament\Resources;

use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Forms;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationLabel = 'カテゴリ';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('名前')->required()->maxLength(255),
            Textarea::make('description')->label('説明')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('名前')->searchable(),
                TextColumn::make('created_at')->label('作成日')->dateTime('Y-m-d H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編集'),
                Tables\Actions\DeleteAction::make()->label('削除'),
            ]);
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
