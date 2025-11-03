<?php

namespace App\Filament\AdminShining\Resources;

use App\Filament\AdminShining\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use UnitEnum;
use BackedEnum;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = '会員一覧';
    protected static ?string $recordTitleAttribute = 'name';

    // Filament の現在の Resources Form API に合わせる
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('名前')->required()->maxLength(100),
            TextInput::make('email')->label('メールアドレス')->email()->required(),
            TextInput::make('phone')->label('電話番号')->maxLength(20),
            DatePicker::make('birthday')->label('生年月日'),
            Toggle::make('is_active')->label('有効会員')->default(true),
            Textarea::make('note')->label('備考')->maxLength(500),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('名前')->searchable(),
                TextColumn::make('email')->label('メール')->searchable(),
                TextColumn::make('phone')->label('電話番号')->searchable(),
                TextColumn::make('is_active')
                    ->label('状態')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? '有効' : '無効')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                TextColumn::make('created_at')->label('登録日')->dateTime('Y-m-d H:i'),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
