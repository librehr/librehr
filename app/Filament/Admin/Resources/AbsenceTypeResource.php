<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources;
use App\Filament\Resources\AbsenceTypeResource\Pages;
use App\Filament\Resources\AbsenceTypeResource\RelationManagers;
use App\Models\AbsenceType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbsenceTypeResource extends Resource
{
    protected static ?string $model = AbsenceType::class;
    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\ColorPicker::make('attributes.color.background')->label('Background Color'),
                Forms\Components\ColorPicker::make('attributes.color.text')->label('Text Color'),
                Forms\Components\Toggle::make('attributes.attachments')->inline(false)->label('Attachments Allowed?'),
                Forms\Components\Toggle::make('attributes.is_holidays')->inline(false)->label('Should discount vacation days?')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\ColorColumn::make('attributes.color.background')->label('Background Color'),
                Tables\Columns\ColorColumn::make('attributes.color.text')->label('Text Color'),
                Tables\Columns\IconColumn::make('attributes.attachments')->label('Attachments Allowed?')->boolean(),
                Tables\Columns\IconColumn::make('attributes.is_holidays')->label('Discount days?')->boolean()
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
            'index' => Resources\AbsenceTypeResource\Pages\ListAbsenceTypes::route('/'),
            'create' => Resources\AbsenceTypeResource\Pages\CreateAbsenceType::route('/create'),
            'edit' => Resources\AbsenceTypeResource\Pages\EditAbsenceType::route('/{record}/edit'),
        ];
    }
}
