<?php

namespace App\Filament\Admin\Resources\DocumentsTypeResource\Pages;

use App\Filament\Admin\Resources\DocumentsTypeResource;
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
