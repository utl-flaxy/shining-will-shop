<?php

namespace App\Filament\Resources;

use App\Models\Category;
use App\Filament\Resources\CategoryResource\Pages as Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return '商品管理';
    }

    public static function getNavigationLabel(): string
    {
        return 'カテゴリー一覧';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-tag';
    }

    // ✅ Filament v4 用に Schema 型へ修正
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('カテゴリ名')
                ->required()
                ->maxLength(100),
            Textarea::make('description')
                ->label('説明')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('カテゴリ名')->searchable(),
                TextColumn::make('created_at')->dateTime('Y-m-d H:i'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
