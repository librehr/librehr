<?php

namespace App\Filament\Resources\TasksCategoryResource\Pages;

use App\Filament\Resources\TasksCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTasksCategory extends EditRecord
{
    protected static string $resource = TasksCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
