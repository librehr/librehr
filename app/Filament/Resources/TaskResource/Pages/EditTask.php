<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('Change Status')
                ->color(Color::Gray)
                ->form([
                    Radio::make('status')
                        ->options(collect(\App\Enums\TaskStatusEnum::cases())
                            ->pluck('name','value'))
                        ->default(fn ($record) => data_get($record, 'status'))
                ])->action(function ($record, $data) {
                    $record->status = data_get($data, 'status');
                    $record->save();
                })
        ];
    }
}
