<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        $options = collect(\App\Enums\TaskStatusEnum::cases())
            ->pluck('name','value');

        // TODO: revisa esto, closed no debe aparecer siempre, solo jefes, managers y admins
        if (data_get($this->getRecord(), 'status') !== 'completed') {
            $options = $options->except(['closed']);
        }

        return $infolist
            ->schema([
                    TextEntry::make('tasksCategory.name')
                        ->label('')
                        ->prefixAction(\Filament\Infolists\Components\Actions\Action::make('Go')
                            ->icon('heroicon-m-arrow-left')
                            ->url(fn () => TaskResource::getNavigationUrl())
                        ->color(Color::Gray))
                        ->inlineLabel(),
                Section::make([
                    Split::make([
                        IconEntry::make('priority')
                            ->icon('heroicon-m-flag')
                            ->columns(1),
                        TextEntry::make('status')
                            ->badge()
                            ->columns(1)
                            ->action(
                                \Filament\Infolists\Components\Actions\Action::make('change')
                                    ->iconButton()
                                    ->button()
                                    ->color(Color::Gray)
                                    ->icon('heroicon-m-chevron-up-down')
                                    ->form([
                                        Radio::make('status')
                                            ->options($options)
                                            ->default(fn ($record) => data_get($record, 'status'))
                                    ])->action(function ($record, $data) {
                                        $record->status = data_get($data, 'status');
                                        $record->save();
                                    })
                            ),
                        TextEntry::make('name')
                            ->columns(1),
                        TextEntry::make('start')
                            ->date()
                            ->columns(1),
                        TextEntry::make('end')
                            ->date()
                            ->columns(1),

                    ]),
                    Section::make([
                        TextEntry::make('description')
                            ->label('Description')
                            ->html()
                            ->placeholder('No description.')
                    ])
                ])
            ]);
    }
    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
