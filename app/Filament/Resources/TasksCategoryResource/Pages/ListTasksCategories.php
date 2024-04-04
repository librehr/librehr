<?php

namespace App\Filament\Resources\TasksCategoryResource\Pages;

use App\Filament\Resources\TasksCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

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
