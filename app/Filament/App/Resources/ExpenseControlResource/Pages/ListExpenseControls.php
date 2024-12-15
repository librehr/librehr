<?php

namespace App\Filament\App\Resources\ExpenseControlResource\Pages;

use App\Filament\App\Resources\ExpenseControlResource;
use App\Filament\App\Resources\ExpenseControlResource\Widgets\ExpenseControlStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListExpenseControls extends ListRecords
{
    protected static string $resource = ExpenseControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseControlStats::class
        ];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pending')
                 ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'allowed' => Tab::make('Accepted')
                 ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'accepted')),
            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
            'denied' => Tab::make('Denied')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'denied')),
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),

        ];
    }
}
