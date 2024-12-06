<?php

namespace App\Filament\Admin\Resources\BusinessResource\Pages;

use App\Filament\Admin\Resources\BusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusiness extends EditRecord
{
    protected static string $resource = BusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
