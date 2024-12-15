<?php

namespace App\Filament\App\Resources\TasksCategoryResource\Pages;

use App\Filament\App\Resources\TasksCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasksCategories extends ListRecords
{
    protected static string $resource = TasksCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
