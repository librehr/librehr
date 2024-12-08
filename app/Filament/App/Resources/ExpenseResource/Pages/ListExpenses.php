<?php

namespace App\Filament\App\Resources\ExpenseResource\Pages;

use App\Filament\App\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\App\Resources\ExpenseResource\Widgets\UserExpenseMonth::class
        ];
    }
}
