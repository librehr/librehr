<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        return parent::mutateFormDataBeforeCreate($data); // TODO: Change the autogenerated stub
    }

    protected function handleRecordCreation(array $data): Model
    {
        return parent::handleRecordCreation($data); // TODO: Change the autogenerated stub
    }
}