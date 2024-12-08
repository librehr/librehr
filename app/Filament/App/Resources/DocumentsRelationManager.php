<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file')
                    ->disk('local')
                    ->multiple()
                    ->directory('documents')
                    ->downloadable()
                    ->storeFileNamesIn('attachment_file_names')
                ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('size')
                ->formatStateUsing(fn ($state) => Number::fileSize($state)),
                Tables\Columns\TextColumn::make('documentable.type.name'),
                Tables\Columns\IconColumn::make('documentable.type.attributes.request_signature')
                    ->label('Sign needed?')
                    ->boolean()
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->with(['documentable.type']);
            });
    }
}
