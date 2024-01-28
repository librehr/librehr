<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Business Configuration';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Select::make('floor_id')
                            ->relationship('floor', 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->with('place'),
                            )
                            ->searchable()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->place->name} -> {$record->name}")
                            ->preload(),
                        Forms\Components\FileUpload::make('attributes.image')
                        ->disk('public')
                    ]),

                ]),
                Forms\Components\Hidden::make('business_id')
                    ->default(\Auth::user()->getActiveBusinessId()),

                Repeater::make('desks')
                    ->relationship()
                    ->schema([
                        Forms\Components\Hidden::make('business_id')
                            ->default(\Auth::user()->getActiveBusinessId()),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('attributes.description')
                            ->label('Intern description'),
                        Checkbox::make('active')
                        ->label('Bookable?')
                    ])->grid(4)
            ])
            ->columns(0);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('floor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor.place.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(fn ($query) => $query->with('desks'));
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
            'map' => Pages\EditRoomMap::route('/{record}/map'),
            'bookings' => Pages\DeskBookings::route('/{record}/bookings'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditRoom::class,
            Pages\EditRoomMap::class,
            Pages\DeskBookings::class,
        ]);
    }
}
