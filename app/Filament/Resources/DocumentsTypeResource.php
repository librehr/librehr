<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentsTypeResource\Pages;
use App\Filament\Resources\DocumentsTypeResource\RelationManagers;
use App\Models\DocumentsType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentsTypeResource extends Resource
{
    protected static ?string $model = DocumentsType::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Administration';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Checkbox::make('attributes.request_signature')
                        ->helperText('If the document needs to be signed or not.')
                ])

                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('attributes.request_signature')
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
            'index' => Pages\ListDocumentsTypes::route('/'),
            'create' => Pages\CreateDocumentsType::route('/create'),
            'edit' => Pages\EditDocumentsType::route('/{record}/edit'),
        ];
    }
}
