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
    /**
     * モデルの関連付け
     */
    protected static ?string $model = Category::class;

    /**
     * ナビゲーション設定
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'カテゴリ一覧';
    protected static ?string $pluralLabel = 'カテゴリ';
    protected static ?string $navigationGroup = '商品管理';
    protected static ?int $navigationSort = 2;

    /**
     * 入力フォーム
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('カテゴリ名')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('例：Tシャツ、タオル、CDなど'),

                    Forms\Components\Textarea::make('description')
                        ->label('説明文')
                        ->rows(3)
                        ->placeholder('カテゴリの簡単な説明を入力'),

                    Forms\Components\Toggle::make('is_visible')
                        ->label('公開ステータス')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('gray'),
                ])
                ->columns(2),

            Forms\Components\Section::make('カテゴリ画像')
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('画像')
                        ->directory('categories')
                        ->image()
                        ->imageEditor()
                        ->maxSize(2048),
                ])
                ->collapsible(),
        ]);
    }

    /**
     * テーブル定義
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('画像')
                    ->square()
                    ->size(60),

                Tables\Columns\TextColumn::make('name')
                    ->label('カテゴリ名')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('公開')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('最終更新')
                    ->dateTime('Y/m/d H:i'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('公開ステータス'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編集'),
                Tables\Actions\DeleteAction::make()->label('削除'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('一括削除'),
                ]),
            ]);
    }

    /**
     * ページ設定
     */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    /**
     * メニューの順番制御用（必要に応じて）
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
