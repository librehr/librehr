<?php

namespace App\Filament\App\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?int $navigationSort = 0;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Human Resources';

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
            \App\Filament\App\Resources\TeamResource\RelationManagers\ContractsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\App\Resources\TeamResource\Pages\ListTeams::route('/'),
            'create' => \App\Filament\App\Resources\TeamResource\Pages\CreateTeam::route('/create'),
            'edit' => \App\Filament\App\Resources\TeamResource\Pages\EditTeam::route('/{record}/edit'),
            'view' => \App\Filament\App\Resources\TeamResource\Pages\ViewTeam::route('/{record}'),
        ];
    }
}
