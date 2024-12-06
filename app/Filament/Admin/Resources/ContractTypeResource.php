<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Resources\ContractTypeResource\Pages;
use App\Filament\Resources\ContractTypeResource\RelationManagers;
use App\Models\ContractType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContractTypeResource extends Resource
{
    protected static ?string $model = ContractType::class;
    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('attributes.vacations')
                ->label('Nª Days for Vacations')
                ->helperText('days per year'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('attributes.vacations')
                ->label('Vacations per year'),
                Tables\Columns\TextColumn::make('contracts')
                    ->label('Contract Assigned')
                ->formatStateUsing(fn ($record) => data_get($record, 'contracts')->count()),
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
            'index' => \App\Filament\Admin\Resources\ContractTypeResource\Pages\ListContractTypes::route('/'),
            'create' => \App\Filament\Admin\Resources\ContractTypeResource\Pages\CreateContractType::route('/create'),
            'edit' => \App\Filament\Admin\Resources\ContractTypeResource\Pages\EditContractType::route('/{record}/edit'),
        ];
    }
}
