<?php

namespace App\Filament\App\Resources\ExpenseControlResource\Pages;

use App\Filament\App\Resources\ExpenseControlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenseControl extends EditRecord
{
    protected static string $resource = ExpenseControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
