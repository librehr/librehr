<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Forms\Components\DocumentField;
use App\Models\Contract;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Human Resources';

    public static function getNavigationBadge(): ?string
    {
        return Contract::query()->activeContracts()->count();
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role->name, ['admin', 'manager']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->required()
                                ->hidden(fn($record) => $record)
                                ->preload(),
                            Forms\Components\DatePicker::make('start')
                                ->required()
                                ->default(now()),
                            Forms\Components\DatePicker::make('end'),
                            Forms\Components\Select::make('team_id')
                                ->relationship('team', 'name')
                                ->searchable()
                                ->required()
                                ->preload(),
                            Forms\Components\Select::make('contract_type_id')
                                ->relationship('contractType', 'name')
                                ->searchable()
                                ->required()
                                ->preload(),
                            Forms\Components\Select::make('place_id')
                                ->relationship('place', 'name')
                                ->searchable()
                                ->required()
                                ->preload(),
                            Forms\Components\Select::make('planning_id')
                                ->relationship('planning', 'name')
                                ->searchable()
                                ->required()
                                ->preload(),
                        ])->columns(2)
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                    Tables\Columns\TextColumn::make('user.name')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('contractType.name'),
                    Tables\Columns\TextColumn::make('team.name')
                        ->hidden(in_array($table->getQueryStringIdentifier(),  ['contractsRelationManager']))
                        ->badge(),
                    Tables\Columns\TextColumn::make('place.name'),
                    Tables\Columns\TextColumn::make('planning.name')
                        ->hidden(in_array($table->getQueryStringIdentifier(),  ['contractsRelationManager'])),
                    Tables\Columns\TextColumn::make('start')->date(),
                    Tables\Columns\TextColumn::make('end')->date()
                        ->hidden(in_array($table->getQueryStringIdentifier(),  ['contractsRelationManager']))
                ]
            )
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
            ])
            ->modifyQueryUsing(function ($query) {
                $query->with('contractType', 'team', 'user');
            });
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\DocumentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
