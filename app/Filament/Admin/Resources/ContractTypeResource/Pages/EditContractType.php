<?php

namespace App\Filament\Admin\Resources\ContractTypeResource\Pages;

use App\Filament\Admin\Resources\ContractTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContractType extends EditRecord
{
    protected static string $resource = ContractTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
