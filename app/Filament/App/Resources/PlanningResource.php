<?php

namespace App\Filament\App\Resources;

use App\Models\Planning;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class PlanningResource extends Resource
{
    protected static ?string $model = Planning::class;
    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'lucide-file-clock';
    protected static ?string $navigationGroup = 'Human Resources';


    public static function form(Form $form): Form
    {
        $dayNames = [];
        $startOfWeek = now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayNames[$startOfWeek->copy()->addDays($i)->format('N')] = $startOfWeek->copy()->addDays($i)->format('l');
        }

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Hidden::make('business_id')
                            ->default(\Auth::user()->getActiveBusinessId()),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Repeater::make('attributes.periods')
                            ->collapsed()
                            ->collapsible()
                            ->itemLabel(fn ($state) => $state['date'])
                            ->cloneable()
                            ->schema([
                                DateRangePicker::make('date')
                                    ->live()
                                    ->displayFormat('D-M')
                                    ->required(),
                                Repeater::make('work_days')
                                    ->collapsible()
                                    ->collapsed()
                                    ->itemLabel(fn ($state) => $dayNames[$state['day']])
                                    ->cloneable()
                                    ->schema([
                                        Select::make('day')
                                            ->live()
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
                    ]),
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
            'index' => \App\Filament\App\Resources\PlanningResource\Pages\ListPlannings::route('/'),
            'create' => \App\Filament\App\Resources\PlanningResource\Pages\CreatePlanning::route('/create'),
            'edit' => \App\Filament\App\Resources\PlanningResource\Pages\EditPlanning::route('/{record}/edit'),
        ];
    }
}
