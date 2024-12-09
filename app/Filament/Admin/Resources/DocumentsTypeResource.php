<?php

namespace App\Filament\Admin\Resources;

use App\Models\DocumentsType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsTypeResource extends Resource
{
    protected static ?string $model = DocumentsType::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Administration';

    protected static bool $isScopedToTenant = false;
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
                        ->disabled()
                        ->helperText('(currently disabled) If the document needs to be signed or not.')
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
            'index' => \App\Filament\Admin\Resources\DocumentsTypeResource\Pages\ListDocumentsTypes::route('/'),
            'create' => \App\Filament\Admin\Resources\DocumentsTypeResource\Pages\CreateDocumentsType::route('/create'),
            'edit' => \App\Filament\Admin\Resources\DocumentsTypeResource\Pages\EditDocumentsType::route('/{record}/edit'),
        ];
    }
}
