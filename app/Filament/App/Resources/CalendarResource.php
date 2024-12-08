<?php

namespace App\Filament\App\Resources;

use App\Filament\Resources\CalendarResource\Pages;
use App\Models\Calendar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CalendarResource extends Resource
{
    protected static ?string $model = Calendar::class;
    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Human Resources';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Hidden::make('business_id')
                        ->default(\Auth::user()->getActiveBusinessId()),
                    Forms\Components\DatePicker::make('date')
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Toggle::make('workable'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('workable')
                    ->boolean(),
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
            'index' => \App\Filament\App\Resources\CalendarResource\Pages\ListCalendars::route('/'),
            'create' => \App\Filament\App\Resources\CalendarResource\Pages\CreateCalendar::route('/create'),
            'edit' => \App\Filament\App\Resources\CalendarResource\Pages\EditCalendar::route('/{record}/edit'),
        ];
    }
}
