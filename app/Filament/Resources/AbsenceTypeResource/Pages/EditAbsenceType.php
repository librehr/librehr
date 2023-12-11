<?php

namespace App\Filament\Resources\AbsenceTypeResource\Pages;

use App\Filament\Resources\AbsenceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsenceType extends EditRecord
{
    protected static string $resource = AbsenceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
