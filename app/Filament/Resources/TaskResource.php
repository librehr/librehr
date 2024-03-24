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
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;
    public static function getNavigationBadge(): ?string
    {
        $taskCount = Task::query()->with('tasksCategory', 'contracts')
            ->whereRelation('contracts', 'contracts.id', \Auth::user()->getActiveContractId())
            ->whereNotIn('status', ['closed', 'completed'])
            ->count();

        return $taskCount > 0 ? $taskCount : null; // TODO: Change the autogenerated stub
    }

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
                            ->options(TaskStatusEnum::class)
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
                        Forms\Components\Select::make('contracts')
                            ->label('Users')
                            ->relationship('contracts', 'id',
                                modifyQueryUsing: fn (Builder $query) => $query->with('user')->where('business_id', \Auth::user()->getActiveBusinessId()),
                            )
                            ->multiple()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => data_get($record, 'user.name'))
                        ,
                        Forms\Components\DatePicker::make('start')->columns(1)
                            ->default(now())->required(),
                        Forms\Components\DatePicker::make('end')->columns(1)
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('attributes.files')
                            ->multiple()
                            ->previewable(false)
                            ->storeFileNamesIn('attributes.fileNames')
                            ->columnSpanFull()
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(fn ($record) => data_get($record, 'end') > now() ? 'bg-red-50' : '')
            ->columns([
                Tables\Columns\IconColumn::make('priority')
                    ->sortable()
                    ->tooltip(fn ($state) => $state->getLabel())
                    ->icon('heroicon-m-flag'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contracts.user.name')
                    ->label('Assigned to')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasksCategory.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')
                    ->date()
                    ->color(fn ($state) => $state > now() ? Color::Red : Color::Gray )
                    ->tooltip(fn ($state) => $state > now() ? 'Outdated' : null )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ])->color(Color::Gray)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                Tables\Filters\Filter::make('not closed')
                    ->query(fn ($query) => $query->where('status', '<>', 'closed'))
                    ->toggle()
                    ->default()
            ])
            ->groups(['priority',
                Group::make('start')->getTitleFromRecordUsing(fn ($record): string => $record->start->format('F d,Y')),
                Group::make('id')->label('Category')->getTitleFromRecordUsing(fn ($record): string => $record->tasksCategory->name),

            ])
            ->paginated([
                50
            ])
            ->modifyQueryUsing(callback: function ($query) {
                $user = \Auth::user();
                $query->with('tasksCategory', 'contracts', 'contracts.user:id,name,attributes');

                if (!in_array($user->role->name, ['admin', 'manager'])) {
                    $query->whereRelation('contracts', 'contracts.id', \Auth::user()->getActiveContractId());
                }

                return $query->orderBy('priority');
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivitiesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'view' => Pages\ViewTask::route('/{record}'),
        ];
    }
}
