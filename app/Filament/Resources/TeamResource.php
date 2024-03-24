<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\ContractsRelationManager;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?int $navigationSort = 0;
    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Business';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\Select::make('supervisors')
                    ->label('Users')
                    ->relationship('supervisors', 'id',
                        modifyQueryUsing: fn (Builder $query) => $query->with('user')->where('business_id', \Auth::user()->getActiveBusinessId()),
                    )
                    ->multiple()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => data_get($record, 'user.name'))
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('contracts_count')
                    ->label('Employeers')
                ->counts('contracts'),
                Tables\Columns\TextColumn::make('supervisors.user.name')
                    ->badge()
                    ->label('Supervisors')

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ])->authorize('update', new Team()),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with('supervisors.user:id,name'));
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ContractsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
            'view' => Pages\ViewTeam::route('/{record}'),
        ];
    }
}
