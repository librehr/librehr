<?php

namespace App\Filament\App\Resources\TasksCategoryResource\Pages;

use App\Filament\App\Resources\TasksCategoryResource;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['parent_id'] === $this->getRecord()->id) {
            return [];
        }

        return parent::mutateFormDataBeforeSave($data); // TODO: Change the autogenerated stub
    }
}