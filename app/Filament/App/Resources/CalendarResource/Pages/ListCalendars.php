<?php

namespace App\Filament\App\Resources\CalendarResource\Pages;

use App\Filament\App\Resources\CalendarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendars extends ListRecords
{
    protected static string $resource = CalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
