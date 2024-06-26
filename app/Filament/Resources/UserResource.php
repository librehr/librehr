<?php

namespace App\Filament\Resources;

use App\Filament\Pages\MyProfile\Profile;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Human Resources';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Details')
            ->schema([
                Forms\Components\TextInput::make('name')->columnSpanFull(),
                Forms\Components\TextInput::make('email'),
                Forms\Components\TextInput::make('password')
                ->password()
                ->helperText('Leave empty if you don\'t want to change it.'),
                Forms\Components\Select::make('role_id')
                    ->relationship('role', 'name')
                    ->default(3),
                Forms\Components\Toggle::make('active')
                    ->default(true),
            ]), Profile::personalForm()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('attributes.avatar')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('role.name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('contracts_count')
                    ->label('Contracts')
                    ->counts('contracts'),
                Tables\Columns\IconColumn::make('active')->boolean(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'documents' => Pages\ManageUserDocuments::route('/{record}/documents'),
            'contracts' => Pages\ManageUserContracts::route('/{record}/contracts'),
            'absences' => Pages\ManageUserAbsences::route('/{record}/absences'),
            'tools' => Pages\ManageUserTools::route('/{record}/tools'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditUser::class,
            Pages\ManageUserContracts::class,
            Pages\ManageUserAbsences::class,
            Pages\ManageUserDocuments::class,
            Pages\ManageUserTools::class,
        ]);
    }
}
