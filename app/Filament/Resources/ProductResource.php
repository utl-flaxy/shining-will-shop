<?php

namespace App\Filament\Resources;

use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Resources\ProductResource\Pages; // ✅ これが正しい

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = '商品管理';
    protected static ?string $pluralModelLabel = '商品';
    protected static ?string $slug = 'products';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->options(Category::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\FileUpload::make('image')
                    ->label('商品画像')
                    ->image()
                    ->directory('products')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->maxSize(10240), // ✅ 10MBまでOK

                Forms\Components\TextInput::make('stock')
                    ->label('在庫数')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->disabled(fn ($record) => filled($record))
                    ->default(fn () => strtoupper('SKU-' . Str::random(6))),

                Forms\Components\Toggle::make('is_published')
                    ->label('公開')
                    ->default(true),

                Forms\Components\Toggle::make('is_active')
                    ->label('有効')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('画像')->square(),
                Tables\Columns\TextColumn::make('name')->label('商品名')->searchable(),
                Tables\Columns\TextColumn::make('price')->label('価格')->money('JPY'),
                Tables\Columns\TextColumn::make('category.name')->label('カテゴリ'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('公開')
                    ->boolean(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('在庫')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 5 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('updated_at')->label('更新日')->dateTime('Y-m-d H:i'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('編集'),
                Tables\Actions\DeleteAction::make()->label('削除'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('一括削除'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
