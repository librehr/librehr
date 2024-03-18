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
        return $infolist
            ->schema([
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
                                    ->color(Color::Gray)
                                    ->icon('heroicon-m-chevron-up-down')
                                    ->form([
                                        Radio::make('status')
                                            ->options(collect(\App\Enums\TaskStatusEnum::cases())
                                                ->pluck('name','value'))
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
                            ->placeholder('No description.')
                    ])
                ])
            ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
