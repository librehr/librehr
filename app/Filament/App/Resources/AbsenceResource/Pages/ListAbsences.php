<?php

namespace App\Filament\App\Resources\AbsenceResource\Pages;

use App\Filament\App\Resources\AbsenceResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAbsences extends ListRecords
{
    protected static string $resource = AbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'allowed' => Tab::make('Allowed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'allowed')),
            'denied' => Tab::make('Denied')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'denied')),
        ];
    }
}
