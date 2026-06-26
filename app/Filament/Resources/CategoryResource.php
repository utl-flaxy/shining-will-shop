<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon  = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'カテゴリ管理';
    protected static ?string $pluralLabel     = 'カテゴリ';
    protected static ?string $navigationGroup = '商品管理';
    protected static ?int    $navigationSort  = 2;

    /* ✅ フォーム（ぐるぐる完全消滅・画像編集復活・プレビュー安定） */
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('name')
                ->label('カテゴリ名')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('説明')
                ->rows(3),

            Forms\Components\Toggle::make('is_active')
                ->label('公開')
                ->default(true),

            Forms\Components\TextInput::make('sort_order')
                ->label('並び順')
                ->numeric()
                ->default(1),

            Forms\Components\FileUpload::make('image')
                ->label('カテゴリ画像')
                ->disk('public')                       // ✅ storage/app/public
                ->directory('categories')             // ✅ categories 配下
                ->image()
                ->imageEditor()                        // ✅ トリミング復活
                ->imagePreviewHeight(150)
                ->visibility('public')
                ->downloadable()
                ->openable()
                ->removeUploadedFileButtonPosition('right')
                ->loadingIndicatorPosition('right'),
        ]);
    }

    /* ✅ 一覧テーブル（画像100%表示・ぐるぐる0） */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([

                Tables\Columns\ImageColumn::make('image')
                    ->label('画像')
                    ->disk('public')                   // ✅ ここが超重要
                    ->square()
                    ->size(60),

                Tables\Columns\TextColumn::make('name')
                    ->label('カテゴリ名')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('公開')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('並び順')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('最終更新')
                    ->dateTime('Y/m/d H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編集'),
                Tables\Actions\DeleteAction::make()
                    ->label('削除')
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
