<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TasksCategoryResource\Pages;
use App\Filament\Resources\TasksCategoryResource\RelationManagers\TasksRelationManager;
use App\Models\TasksCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class TasksCategoryResource extends Resource
{
    protected static ?string $model = TasksCategory::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationGroup = 'Administration';
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
                    ->url(fn ($record) => route(Pages\ListTasksCategories::getRouteName('app'), [
                        'tableFilters[parent][id]' => data_get($record, 'id')
                    ]))
                    ->iconButton(),
                Tables\Actions\Action::make('Add SubCategory')
                    ->icon('heroicon-m-plus-circle')
                    ->iconButton()
                    ->size('xl')
                    ->form(fn ($form) => [self::form($form)])
                    ->action(function ($record, $data) {
                        $created = TasksCategory::query()->create([
                            'name' => data_get($data, 'name'),
                            'business_id' => data_get($record, 'business_id'),
                            'parent_id' => data_get($record, 'id')
                        ]);

                        redirect()->to(
                            route(Pages\EditTasksCategory::getRouteName('app'), data_get($created, 'id'))
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
            'index' => Pages\ListTasksCategories::route('/'),
            'create' => Pages\CreateTasksCategory::route('/create'),
            'edit' => Pages\EditTasksCategory::route('/{record}/edit'),
        ];
    }
}
