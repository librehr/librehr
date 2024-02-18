<?php

namespace App\Filament\Resources\DocumentsTypeResource\Pages;

use App\Filament\Resources\DocumentsTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentsType extends EditRecord
{
    protected static string $resource = DocumentsTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
