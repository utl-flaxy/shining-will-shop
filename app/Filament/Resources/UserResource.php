<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = '会員管理';

    protected static ?string $navigationGroup = 'ショップ管理';

    protected static ?string $modelLabel = '会員';

    protected static ?string $pluralModelLabel = '会員';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->label('名前')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('メールアドレス')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('パスワード')
                    ->password()
                    ->dehydrateStateUsing(
                        fn ($state) => filled($state)
                            ? Hash::make($state)
                            : null
                    )
                    ->dehydrated(
                        fn ($state) => filled($state)
                    )
                    ->required(
                        fn (string $operation) => $operation === 'create'
                    )
                    ->maxLength(255),

                Toggle::make('is_admin')
                    ->label('管理者')
                    ->default(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('名前')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('メールアドレス')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->label('管理者')
                    ->boolean(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('注文数')
                    ->counts('orders')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('登録日')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

            ])

            ->filters([

                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('管理者'),

            ])

            ->actions([

                Tables\Actions\EditAction::make(),

            ])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);
    }

    public static function getRelations(): array
    {
        return [

            RelationManagers\OrdersRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [

            'index' => Pages\ListUsers::route('/'),

            'create' => Pages\CreateUser::route('/create'),

            'edit' => Pages\EditUser::route('/{record}/edit'),

        ];
    }
}
