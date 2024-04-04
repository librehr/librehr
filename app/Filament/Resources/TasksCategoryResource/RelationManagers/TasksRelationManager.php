<?php

namespace App\Filament\Resources\TasksCategoryResource\RelationManagers;

use App\Filament\Resources\TaskResource;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return TaskResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(TaskResource::table($table)->getColumns())
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('View Task')
                    ->button()
                    ->url(fn ($record) =>
                    route(TaskResource\Pages\ViewTask::getRouteName('app'), [
                        'record' => data_get($record, 'id')
                    ]))
            ])
            ->actionsPosition(Tables\Enums\ActionsPosition::AfterColumns)
            ->bulkActions([

            ])
            ->selectable(false);
    }
}
