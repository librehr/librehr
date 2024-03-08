<?php

namespace App\Filament\Resources\TasksCategoryResource\Pages;

use App\Filament\Resources\TasksCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTasksCategory extends CreateRecord
{
    protected static string $resource = TasksCategoryResource::class;
}
