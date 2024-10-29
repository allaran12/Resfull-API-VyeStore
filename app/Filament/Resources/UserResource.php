<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'user';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('profile_image')
                    ->disk('public')
                    ->directory('profile')
                    ->image()
                    ->nullable(),

                TextInput::make('name')
                    ->required()
                    ->label('Nama'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->label('Email'),

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->dehydrateStateUsing(static fn($state) => \Hash::make($state))
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateUser),

                // Toggle::make('isDisable')
                //     ->label('Banned'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->disk('public')
                    ->url(fn($record) => $record->profile_image ? Storage::disk('public')->url($record->profile_image) : null)
                    ->label('profil Image'),

                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                ToggleColumn::make('isDisable')
                    ->label('Banned'),
            ])
            ->filters([
                // Uncomment and adjust if you want to add a filter for roles
                // Tables\Filters\Filter::make('role')
                //     ->query(fn(Builder $query) => $query->where('role', 'user'))
                //     ->label('Role: User'),

                // Tables\Filters\Filter::make('role')
                //     ->query(fn(Builder $query) => $query->where('role', 'users'))
                //     ->label('Role: Users'),
                Filter::make('Di banned')
                    ->query(fn(Builder $query): Builder => $query->where('isDisable', true))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getTableQuery(): Builder
    {
        return User::query()->where('role', 'users');
    }

    public static function getRelations(): array
    {
        return [
            // Define relationships if needed
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', '=', 'users');
    }
}
