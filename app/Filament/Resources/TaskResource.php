<?php

namespace App\Filament\Resources;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make([

                ])->columnSpanFull(),
                Forms\Components\Section::make('Task')
                    ->schema([
                        Forms\Components\Hidden::make('business_id')
                            ->default(\Auth::user()->getActiveBusinessId()),
                        Forms\Components\Select::make('status')
                            ->options(collect(TaskStatusEnum::cases())->pluck('name', 'value'))
                            ->required()
                            ->columns(1)
                            ->default('open'),
                        Forms\Components\Select::make('priority')
                            ->options(collect(TaskPriorityEnum::cases())->pluck('name', 'value'))
                            ->required()
                            ->columns(1)
                            ->default(3),
                        Forms\Components\Select::make('tasks_category_id')
                            ->searchable()
                            ->preload()
                            ->relationship('tasksCategory', 'name')
                            ->required()
                            ->columns(1),
                        Forms\Components\DatePicker::make('start')->columns(1)
                        ->default(now())->required(),
                        Forms\Components\DatePicker::make('end')->columns(1),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->autosize(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('priority')
                    ->sortable()
                    ->icon('heroicon-m-flag'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasksCategory.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Filter::make('my_tasks')
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->whereRelation('users', 'users.id', \Auth::id()))
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('goto')->url(fn ($record) => 'tasks/' . $record->id),
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups(['start'])
            ->modifyQueryUsing(fn ($query) => $query->with('tasksCategory')
                ->orderBy('start')
                ->orderBy('priority'));
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'view' => Pages\TaskActivity::route('/{record}'),
        ];
    }
}
