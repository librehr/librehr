<?php

namespace App\Filament\Admin\Resources\AbsenceTypeResource\Pages;

use App\Filament\Admin\Resources\AbsenceTypeResource;
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
