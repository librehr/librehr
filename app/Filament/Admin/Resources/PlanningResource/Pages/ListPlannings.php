<?php

namespace App\Filament\Admin\Resources\PlanningResource\Pages;

use App\Filament\Admin\Resources\PlanningResource;
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
