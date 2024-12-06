<?php

namespace App\Filament\Admin\Resources;

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

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static bool $isScopedToTenant = false;
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
            'index' => \App\Filament\Admin\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Admin\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Admin\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
            'documents' => \App\Filament\Admin\Resources\UserResource\Pages\ManageUserDocuments::route('/{record}/documents'),
            'contracts' => \App\Filament\Admin\Resources\UserResource\Pages\ManageUserContracts::route('/{record}/contracts'),
            'absences' => \App\Filament\Admin\Resources\UserResource\Pages\ManageUserAbsences::route('/{record}/absences'),
            'tools' => \App\Filament\Admin\Resources\UserResource\Pages\ManageUserTools::route('/{record}/tools'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            \App\Filament\Admin\Resources\UserResource\Pages\EditUser::class,
            \App\Filament\Admin\Resources\UserResource\Pages\ManageUserContracts::class,
            \App\Filament\Admin\Resources\UserResource\Pages\ManageUserAbsences::class,
            \App\Filament\Admin\Resources\UserResource\Pages\ManageUserDocuments::class,
            \App\Filament\Admin\Resources\UserResource\Pages\ManageUserTools::class,
        ]);
    }
}
