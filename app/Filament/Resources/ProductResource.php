<?php

namespace App\Filament\Resources;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Resources\ProductResource\Pages;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = '商品管理';
    protected static ?string $pluralModelLabel = '商品';
    protected static ?string $slug = 'products';
    protected static ?string $recordTitleAttribute = 'name';

    /* =========================
        ✅ 商品フォーム
    ========================= */

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('name')
                ->label('商品名')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('商品説明')
                ->rows(3)
                ->maxLength(1000),

            Forms\Components\TextInput::make('price')
                ->label('価格')
                ->numeric()
                ->required()
                ->prefix('¥'),

            Forms\Components\Select::make('category_id')
                ->label('カテゴリ')
                ->relationship('category', 'name')
                ->searchable()
                ->required(),

            /** ✅ product_images 用 画像アップロード **/
            Forms\Components\FileUpload::make('images')
                ->label('商品画像')
                ->image()
                ->multiple()
                ->directory('products')
                ->disk('public')
                ->reorderable()
                ->preserveFilenames()
                ->maxSize(10240),

            Forms\Components\TextInput::make('stock')
                ->label('通常在庫')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),

            Forms\Components\TextInput::make('sku')
                ->label('SKU')
                ->disabled(fn ($record) => filled($record))
                ->default(fn () => strtoupper('SKU-' . Str::random(6))),

            Forms\Components\Toggle::make('manage_stock')
                ->label('在庫管理を有効化')
                ->default(true),

            Forms\Components\Toggle::make('is_published')
                ->label('公開')
                ->default(true),

            Forms\Components\Toggle::make('is_active')
                ->label('有効')
                ->default(true),
        ]);
    }

    /* =========================
        ✅ 一覧
    ========================= */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // ✅ product_images から取得
                Tables\Columns\ImageColumn::make('images.url')
                    ->label('画像')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('商品名')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('価格')
                    ->money('JPY'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('カテゴリ'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('公開')
                    ->boolean(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('通常在庫')
                    ->badge(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新日')
                    ->dateTime('Y-m-d H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編集'),
                Tables\Actions\DeleteAction::make()->label('削除'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
