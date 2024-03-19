<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')->default(\Auth::id()),
                Forms\Components\RichEditor::make('attributes.body')
                    ->label('Message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('attributes.files')
                    ->multiple()
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('attributes.body')
                    ->label('Message')
                    ->html()
                    ->columnSpanFull()
                    ->description(fn ($record) => data_get($record, 'created_at')->format('F Y, H:s'))
                    ->label('Message'),
                Tables\Columns\ImageColumn::make('attributes.files')
                    ->label('Attachments')
                    ->stacked()
                    ->circular()
                    ->ring(5)
                    ->overlap(2)
                    ->wrap()
                    ->limit(3)
                    ->checkFileExistence(false)
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->limitedRemainingText(size: 'lg')
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->authorize(true),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->authorize(fn ($record) => $record->user_id === \Auth::id()),
                    Tables\Actions\DeleteAction::make()->authorize(fn ($record) => $record->user_id === \Auth::id()),
                    Tables\Actions\ViewAction::make()->authorize(true),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
