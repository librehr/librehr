<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenceResource\Pages;
use App\Filament\Resources\AbsenceResource\RelationManagers;
use App\Models\Absence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class AbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('absence_type_id')
                        ->relationship('absenceType', 'name')
                        ->required()
                        ->columnSpan(3),
                    DateRangePicker::make('date')
                        ->formatStateUsing(function ($record) {
                            if ($record) {
                                return $record->start->format('d/m/Y') . ' - ' . $record->end->format('d/m/Y');
                            }
                            return null;
                        })
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\Textarea::make('comments')->columnSpanFull()
                ])->columns(6),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('absenceType.name')
                ->label('Type')
                ->searchable(),
                Tables\Columns\TextColumn::make('contract.user.name'),
                Tables\Columns\TextColumn::make('start')
                    ->formatStateUsing(fn ($state) => $state->format('d/m/Y')),
                Tables\Columns\TextColumn::make('end')
                    ->formatStateUsing(fn ($state) => $state->format('d/m/Y')),
                Tables\Columns\TextColumn::make('status')
            ])
            ->filters([
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
            \App\Filament\Resources\DocumentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsences::route('/'),
            'create' => Pages\CreateAbsence::route('/create'),
            'edit' => Pages\EditAbsence::route('/{record}/edit'),
        ];
    }
}
