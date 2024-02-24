<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeskBookings extends ManageRelatedRecords
{
    protected static string $resource = RoomResource::class;

    protected static string $relationship = 'deskBookings';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Bookings';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('contract.user.name'),
                Tables\Columns\TextColumn::make('desk.name')->badge()->color(Color::Blue),
                Tables\Columns\TextColumn::make('start')->date('d/m/Y H:i:s'),
                Tables\Columns\TextColumn::make('end')->date('d/m/Y H:i:s'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reserved at')
                    ->date('d/m/Y H:i:s'),
            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->with('contract.user');
            });
    }
}
