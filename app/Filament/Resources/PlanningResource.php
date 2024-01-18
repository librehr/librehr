<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanningResource\Pages;
use App\Filament\Resources\PlanningResource\RelationManagers;
use App\Models\Planning;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class PlanningResource extends Resource
{
    protected static ?string $model = Planning::class;


    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Business Configuration';

    public static function form(Form $form): Form
    {
        $dayNames = [];
        $startOfWeek = now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayNames[$startOfWeek->copy()->addDays($i)->format('N')] = $startOfWeek->copy()->addDays($i)->format('l');
        }

        return $form
            ->schema([
                Forms\Components\Hidden::make('business_id')
                    ->default(\Auth::user()->getActiveBusinessId()),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Repeater::make('attributes.periods')
                    ->cloneable()
                    ->schema([
                        DateRangePicker::make('date')
                            ->displayFormat('D-M')
                            ->required(),
                        Repeater::make('work_days')
                            ->cloneable()
                            ->schema([
                                Select::make('day')
                                    ->options($dayNames)
                                    ->required(),
                                Repeater::make('times')
                                    ->cloneable()
                                    ->schema([
                                        Forms\Components\TimePicker::make('from')
                                            ->step('h')
                                            ->closeOnDateSelection()
                                            ->required(),
                                        Forms\Components\TimePicker::make('to')
                                            ->step('h')
                                            ->required(),
                                    ])
                                    ->columns(2)

                            ])
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            ])->modifyQueryUsing(fn ($query) => $query->where('business_id', \Auth::user()->getActiveBusinessId()));
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
            'index' => Pages\ListPlannings::route('/'),
            'create' => Pages\CreatePlanning::route('/create'),
            'edit' => Pages\EditPlanning::route('/{record}/edit'),
        ];
    }
}
