<?php

namespace App\Filament\App\Resources\TeamResource\RelationManagers;

use App\Filament\App\Resources\ContractResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';
    protected static ?string $label = 'Employees';
    protected static ?string $title = 'Employees';

    protected function canCreate(): bool
    {
        return  false;
    }

    protected function canDelete(Model $record): bool
    {
        return false;
    }

    protected function canEdit(Model $record): bool
    {
        return  false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns(ContractResource::table($table)->getColumns())
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actºions([
                Tables\Actions\Action::make('Contract')
                    ->tooltip('view_contract')
                    ->iconButton()
                    ->icon('heroicon-o-document')
                    ->url(fn ($record) => ContractResource\Pages\EditContract::getNavigationUrl([
                        $record->id
                    ])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([

            ])->selectable(false);
    }
}
