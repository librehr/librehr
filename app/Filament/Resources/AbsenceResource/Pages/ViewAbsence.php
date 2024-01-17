<?php

namespace App\Filament\Resources\AbsenceResource\Pages;

use App\Filament\Resources\AbsenceResource;
use App\Filament\Resources\CalendarWidgetResource\Widgets\CalendarWidget;
use App\Models\Absence;
use App\Services\Absences;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;

class ViewAbsence extends ViewRecord
{
    protected static string $resource = AbsenceResource::class;

    public function getFooterWidgets(): array
    {
        return [
            CalendarWidget::make([
                'absence' => $this->getRecord()
            ])
        ]; // TODO: Change the autogenerated stub
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept')
                ->icon('heroicon-m-check')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(function ($record) {
                    return data_get($record, 'status.value') === 'pending' ? false : true;
                })
                ->action(function ($record) {
                    $record->status_by = \Auth::id();
                    $record->status_at = now();
                    $record->status = 'allowed';
                    $record->save();

                    Notification::make()
                        ->title('Accepted successfully')
                        ->success()
                        ->send();

                    $record->requests()->detach();
                }),
            Action::make('denie')
                ->icon('heroicon-m-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(function ($record) {
                    return data_get($record, 'status.value') === 'pending' ? false : true;
                })
                ->action(function ($record) {
                    $record->status_by = \Auth::id();
                    $record->status_at = now();
                    $record->status = 'denied';
                    $record->save();

                    Notification::make()
                        ->title('Denied successfully')
                        ->success()
                        ->send();

                    $record->requests()->detach();
                }),
        ];
    }
}

