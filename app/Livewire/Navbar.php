<?php

namespace App\Livewire;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navbar extends Component
{
    public $businesses = null;
    public $activeBusiness = null;

    public function mount()
    {
        $this->businesses = Business::query()
            ->where('active', true)->get();
        $this->getActiveBusiness();
    }

    public function create()
    {
        return $this->redirect(route('filament.app.resources.businesses.create'));
    }

    public function setBusiness($uuid)
    {
        $user = Auth::user();
        $attributes = $user->attributes;
        $attributes['default_business'] = $uuid;
        $user->attributes = $attributes;
        $user->save();
        $this->getActiveBusiness($uuid);
        return $this->redirect(route('filament.app.pages.dashboard'));

    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <x-filament::dropdown>
                <x-slot name="trigger">
                    <x-filament::button color="gray">
                        <div class="flex flex-col">
                            <span>
                                {!! $activeBusiness ? '<b>Business:</b> ' . $activeBusiness->name : 'Business' !!}
                            </span>
                            @if(Auth::user()->getActiveContract())
                            <span class="text-xs">
                                 from {{ data_get(Auth::user()->getActiveContract(), 'start')->format('d/m/Y') }}
                            </span>
                            @endif
                        </div>
                    </x-filament::button>
                </x-slot>

                <x-filament::dropdown.list>
                    @forelse($this->businesses as $business)
                        <x-filament::dropdown.list.item wire:click="setBusiness('{{ $business->uuid }}');">
                            {{ $business->name }}
                        </x-filament::dropdown.list.item>
                    @empty
                        <x-filament::dropdown.list.item wire:click="create">
                            Create Business
                        </x-filament::dropdown.list.item>
                    @endforelse



                </x-filament::dropdown.list>
            </x-filament::dropdown>
        </div>
        HTML;
    }

    private function getActiveBusiness($uuid = null)
    {
        if ($uuid === null) {
            $uuid = data_get(Auth::user(), 'attributes.default_business');
        }
        $this->activeBusiness = Business::query()
            ->where('active', true)
            ->where('uuid', $uuid)->first();
    }
}
