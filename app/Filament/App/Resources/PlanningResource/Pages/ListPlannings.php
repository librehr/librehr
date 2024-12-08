<?php

namespace App\Filament\App\Resources\PlanningResource\Pages;

use App\Filament\App\Resources\PlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlannings extends ListRecords
{
    protected static string $resource = PlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
