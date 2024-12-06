<?php

namespace App\Filament\Admin\Resources\TasksCategoryResource\Pages;

use App\Filament\Admin\Resources\TasksCategoryResource;
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
