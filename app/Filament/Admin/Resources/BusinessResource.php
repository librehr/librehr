<?php

namespace App\Filament\Admin\Resources;

use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;
    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name'),
                    Forms\Components\FileUpload::make('attributes.logo')
                        ->image()
                        ->disk('public')
                        ->imageEditor(),
                ]),
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('attributes.default_vacations')
                        ->default(config('librehr.vacations'))
                        ->numeric(),
                    Forms\Components\TextInput::make('attributes.default_currency')
                        ->default(config('librehr.currency'))
                        ->helperText(new HtmlString('Must be a valid ISO currency <a target="_blank" class="font-bold text-primary-600" href="https://www.iban.com/currency-codes">View list</a>')),
                    Forms\Components\TextInput::make('attributes.default_timezone')
                        ->default(config('librehr.timezone'))
                        ->helperText(new HtmlString('Must be a valid timezone <a target="_blank" class="font-bold text-primary-600" href="https://www.php.net/manual/en/timezones.php">View list</a>')),
                ])
                    ->heading('Business Configuration')
                    ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\ImageColumn::make('attributes.logo')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('attributes.default_vacations')
                    ->label('Default Vacations'),
                Tables\Columns\TextColumn::make('attributes.default_currency')
                    ->label('Default Currency'),
                Tables\Columns\TextColumn::make('attributes.default_timezone')
                    ->label('Default Timezone'),
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
            'index' => \App\Filament\Admin\Resources\BusinessResource\Pages\ListBusinesses::route('/'),
            'create' => \App\Filament\Admin\Resources\BusinessResource\Pages\CreateBusiness::route('/create'),
            'edit' => \App\Filament\Admin\Resources\BusinessResource\Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
