<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class EditRoomMap extends Page
{
    use InteractsWithRecord;

    protected static ?string $navigationIcon = 'heroicon-m-map';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->dispatch('refreshMap');
    }

    protected static string $resource = RoomResource::class;

    protected static string $view = 'filament.resources.room-resource.pages.edit-room-map';


    #[On('add-circle')]
    public function addCircle()
    {
        $this->dispatch('refreshMap');
    }

    #[On('delete-circle')]
    public function deleteCircle()
    {
        \Log::error('ee');
        $this->dispatch('refreshMap');

    }
}
