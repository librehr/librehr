<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TasksCategoryResource\RelationManagers\TasksRelationManager;
use App\Models\TasksCategory;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TasksCategoryResource extends Resource
{
    protected static ?string $model = TasksCategory::class;
    protected static bool $isScopedToTenant = false;
    protected static ?string $navigationGroup = 'Human Resources';

    protected static ?string $navigationIcon = 'lucide-layout-list';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('business_id')
                    ->default(\Auth::user()->getActiveBusinessId()),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->sortable(),
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
                Tables\Filters\Filter::make('parent'),
                Tables\Filters\SelectFilter::make('parent')
                    ->label('Parent')
                    ->indicateUsing(fn ($data, $state) => data_get(TasksCategory::query()->find(data_get($data, 'id')), 'name'))
                    ->form([
                        Forms\Components\Select::make('id')
                            ->relationship('parent', 'name'),
                    ])->query(function (Builder $query, array $data, $state): Builder {
                        return $query->where('parent_id', $data['id']);
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('Open Category')
                    ->icon('heroicon-m-folder')
                    ->color(Color::Gray)
                    ->url(fn ($record) => route(\App\Filament\App\Resources\TasksCategoryResource\Pages\ListTasksCategories::getRouteName('app'), [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[parent][id]' => data_get($record, 'id')
                    ]))
                    ->iconButton(),
                Tables\Actions\Action::make('Add SubCategory')
                    ->icon('heroicon-m-plus-circle')
                    ->iconButton()
                    ->size('xl')
                    ->form(fn ($form) => self::form($form)->getComponents())
                    ->action(function ($record, $data) {
                        $created = TasksCategory::query()->create([
                            'name' => data_get($data, 'name'),
                            'business_id' => data_get($record, 'business_id'),
                            'parent_id' => data_get($record, 'id')
                        ]);

                        redirect()->to(
                            route(\App\Filament\App\Resources\TasksCategoryResource\Pages\EditTasksCategory::getRouteName('app'), [
                                Filament::getTenant()->id, data_get($created, 'id')
                            ])
                        );
                    }),
            ])
            ->actionsPosition(Tables\Enums\ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $tFilters = request()->all();
                if (empty($tFilters)) {
                    $query->whereNull('parent_id');
                }

            });
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\App\Resources\TasksCategoryResource\Pages\ListTasksCategories::route('/'),
            'create' => \App\Filament\App\Resources\TasksCategoryResource\Pages\CreateTasksCategory::route('/create'),
            'edit' => \App\Filament\App\Resources\TasksCategoryResource\Pages\EditTasksCategory::route('/{record}/edit'),
        ];
    }
}
