<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Games;
use App\Models\Genre;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GamesResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GamesResource\RelationManagers;

class GamesResource extends Resource
{
    protected static ?string $model = Games::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Daftar Game';

    protected static ?string $navigationGroup = 'Game';


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
                ->schema([
                    Section::make('form')->collapsible()->description('data game')->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Game Name')
                            ->live()
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('surname', Str::slug($state)))
                            ,
                        TextInput::make('surname')
                            ->required()
                            ->label('Slug'),
                        TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->label('Price'),
                        TextInput::make('link')
                            ->required()
                            ->label('Download Link'),
                        TextInput::make('release')
                            ->required()
                            ->label('Release Date'),
                        TextInput::make('platforms')
                            ->required()
                            ->label('Platforms'),
                        TextInput::make('developers')
                            ->required()
                            ->label('Developers'),
                        TextInput::make('publishers')
                            ->required()
                            ->label('Publishers'),
                        RichEditor::make('desc')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ])->columnSpan(2)->columns(2),

                    Group::make()->schema([
                        Section::make("image")
                            ->collapsible()
                            ->schema([
                                FileUpload::make('cover')
                                    ->disk('public')
                                    ->directory('covers')
                                    ->image()
                                    ->nullable(),
                                FileUpload::make('footage')
                                    ->disk('public')
                                    ->directory('footage')
                                    ->multiple()
                                    ->image()
                                    ->nullable(),
                            ])->columns(1),
                        Section::make("data")->collapsible()->schema([
                            TextInput::make('rating')
                                ->numeric()
                                ->required()
                                ->label('Rating'),
                            Select::make('genre')
                                ->options(Genre::all()->pluck('name', 'name'))
                                ->multiple()
                                ->searchable()
                                ->label('Genre')
                                ->afterStateUpdated(function ($state) {
                                    return json_encode($state);
                                }),
                        ]),

                    ])->columnSpan(1),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_number')
                ->label('No.')
                ->disabled()
                ->rowIndex(),
                ImageColumn::make('cover')
                    ->disk('public')
                    ->url(fn($record) => $record->cover ? Storage::disk('public')->url($record->cover) : null)
                    ->label('Cover Image'),
                TextColumn::make('name') 
                    ->sortable()
                    ->searchable()
                    ->label('Game Name'),
                TextColumn::make('genre')
                    ->sortable()
                    ->searchable()
                    ->label('genre'),
                TextColumn::make('price')
                    ->sortable()
                    ->label('Price'),
                TextColumn::make('platforms')
                    ->sortable()
                    ->label('Platforms'),
                
            ])
            ->filters([
                //  SelectFilter::make('genre')
                // ->label('Genre')
                // ->options(
                //     Genre::pluck('name', 'name')->toArray() // Mendapatkan opsi filter dari nama genre
                // )
                // ->query(function (Builder $query, $value) {
                //     dd($value);
                //     if ($value) {
                //         // Pastikan bahwa $value tersedia sebelum digunakan dalam query
                //         return $query->whereJsonContains('genres', $value);
                //     }
                //     return $query;
                // }),

                SelectFilter::make("genre")->relationship('genre','name')

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
            //
        ];
    }

     


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGames::route('/create'),
            'edit' => Pages\EditGames::route('/{record}/edit'),
        ];
    }

   
}
