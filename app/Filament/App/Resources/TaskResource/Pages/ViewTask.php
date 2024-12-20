<?php

namespace App\Filament\App\Resources\TaskResource\Pages;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Filament\App\Resources\TaskResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record); // TODO: Change the autogenerated stub
    }

    public function getTitle(): string|Htmlable
    {
        return data_get($this->record, 'name');
    }

    public function getBreadcrumb(): string
    {
        return data_get($this->record, 'name');
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('Assign Users')
                ->icon('heroicon-m-users')
                ->color(Color::Gray)
                ->form([
                    Select::make('contracts')
                        ->label('Users')
                        ->relationship(
                            'contracts',
                            'id',
                            modifyQueryUsing: fn (Builder $query) => $query->with('user')->where('business_id', \Auth::user()->getActiveBusinessId()),
                        )
                        ->multiple()
                        ->preload()
                        ->default(fn ($record) => data_get($record, 'contracts')?->pluck('id')->toArray())
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => data_get($record, 'user.name'))
                        ->mutateDehydratedStateUsing(fn () => dd('hola'))
                    ,
                ])->action(function ($record) {
                    $task = $record->load('contracts');

                    Notification::make()
                        ->title('Users assigned saved sucessfully.')
                        ->success()
                        ->send();
                })
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('end')
                    ->label('')
                    ->extraAttributes([
                        'class' => 'rounded-2xl p-4 px-6 border border-gray-200 bg-red-50'
                    ])
                    ->color(Color::Gray)
                    ->size('sm')
                    ->iconColor(Color::Red)
                    ->icon('heroicon-o-exclamation-triangle')
                    ->columnSpanFull()
                    ->tooltip(fn ($state) => Carbon::parse($state)->toDate() < now()->toDate() ? 'Out dated' : null)
                    ->visible(fn ($state) => Carbon::parse($state)->toDate() < now())
                    ->formatStateUsing(fn ($state) => new HtmlString('This task is outdated: <span class="text-primary-600">' . Carbon::parse($state)->format('F d, Y') . '</span>'))
                    ->columns(1),
                Section::make([
                    Split::make([
                        IconEntry::make('priority')
                            ->tooltip('Change Priority')
                            ->action(
                                \Filament\Infolists\Components\Actions\Action::make('Change Priority')
                                ->form([
                                    Radio::make('priority')
                                        ->options(TaskPriorityEnum::class)
                                        ->default(fn ($record) => data_get($record, 'priority'))
                                ])->action(function ($data, $record) {
                                    $record->priority = data_get($data, 'priority');
                                    $record->save();

                                    Notification::make()
                                        ->title('Priority saved sucessfully.')
                                        ->success()
                                        ->send();
                                })
                            )
                            ->icon('heroicon-m-flag')
                            ->columns(1),
                        TextEntry::make('status')
                            ->tooltip('Change Status')
                            ->badge()
                            ->columns(1)
                            ->action(
                                \Filament\Infolists\Components\Actions\Action::make('Change Status')
                                    ->iconButton()
                                    ->button()
                                    ->color(Color::Gray)
                                    ->icon('heroicon-m-chevron-up-down')
                                    ->form([
                                        Radio::make('status')
                                            ->options(TaskStatusEnum::class)
                                            ->default(fn ($record) => data_get($record, 'status'))
                                    ])->action(function ($record, $data) {
                                        $record->status = data_get($data, 'status');
                                        $record->save();

                                        Notification::make()
                                            ->title('Status saved sucessfully.')
                                            ->success()
                                            ->send();
                                    })
                            ),
                        TextEntry::make('contracts.*.user.name')
                            ->label('Change assignation')
                            ->tooltip('Change assignation')
                            ->badge()
                            ->color(Color::Gray)
                            ->action(
                                \Filament\Infolists\Components\Actions\Action::make('Change Assignation')
                                    ->icon('heroicon-m-users')
                                    ->color(Color::Gray)
                                    ->form([
                                    Select::make('contracts')
                                        ->label('Users')
                                        ->relationship(
                                            'contracts',
                                            'id',
                                            modifyQueryUsing: fn (Builder $query) => $query->with('user')->where('business_id', \Auth::user()->getActiveBusinessId()),
                                        )
                                        ->multiple()
                                        ->preload()
                                        ->default(fn ($record) => data_get($record, 'contracts')?->pluck('id')->toArray())
                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => data_get($record, 'user.name'))
                                    ,
                                ])->action(function ($record) {
                                    Notification::make()
                                        ->title('Users assigned saved sucessfully.')
                                        ->success()
                                        ->send();
                                })
                            )
                            ->label('Assigned to'),
                        TextEntry::make('tasksCategory.name')
                            ->label('Category')
                            ->color(Color::Gray)
                            ->columns(1),
                        TextEntry::make('start')
                            ->date()
                            ->color(Color::Gray)
                            ->columns(1),
                        TextEntry::make('end')
                            ->date()
                            ->color(fn ($state) => Carbon::parse($state)->toDate() < now()->toDate() ? Color::Red : Color::Gray)
                            ->tooltip(fn ($state) => Carbon::parse($state)->toDate() < now()->toDate() ? 'Outdated' : null)
                            ->columns(1),
                    ])->columns(5),
                    Section::make([
                        TextEntry::make('description')
                            ->label('Description')
                            ->html()
                            ->extraAttributes([
                                'contenteditable' => '',
                                'class' => 'p-1 focus:ring-0',
                            ])
                            ->placeholder('No description.'),
                        RepeatableEntry::make('attributes.files')
                            ->label('Attached Files')
                            ->schema([
                                TextEntry::make('')
                                    ->icon('heroicon-m-arrow-down-tray')
                                    ->formatStateUsing(fn ($record, $state) => data_get($record, 'attributes.fileNames')[$state] ?? null)
                                    ->url(
                                        fn ($state) =>
                                        \Storage::url($state),
                                        true
                                    )->extraAttributes([
                                        'target' => '_blank'
                                    ])
                            ])->hidden(fn ($record) => empty(data_get($record, 'attributes.files', [])))
                    ])
                ])
            ]);
    }
}
