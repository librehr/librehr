<?php

namespace App\Filament\Resources\PlanningResource\Pages;

use App\Filament\Resources\PlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanning extends EditRecord
{
    protected static string $resource = PlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
