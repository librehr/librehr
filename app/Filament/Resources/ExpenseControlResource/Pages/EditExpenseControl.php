<?php

namespace App\Filament\Resources\ExpenseControlResource\Pages;

use App\Filament\Resources\ExpenseControlResource;
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
