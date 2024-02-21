<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\Contract;
use App\Models\Scopes\BusinessScope;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navbar extends Component
{
    public $businesses = null;
    public $activeBusiness = null;

    public function mount()
    {
        $user = Auth::user();
        $this->businesses = $user->isAdmin ? Business::query()->where('active', true)->get() : $this->getBusinesses($user->id);
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

        \Cache::forget('active_contract' . $user->id);
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
                        <div class="flex flex-row gap-2 items-center min-w-[150px]">
                            @if(Auth::user()->getActiveContract())
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            @endif
                            <span>
                                {!! $activeBusiness ? $activeBusiness->name : 'Business' !!}
                            </span>
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
        $user = Auth::user();
        $this->activeBusiness = $user->getActiveBusiness();
    }

    private function getBusinesses($userId)
    {
        return Contract::withoutGlobalScope(BusinessScope::class)
            ->with('business')
            ->where('user_id', $userId)
            ->get()
            ->pluck('business')
            ->unique()
            ->where('active', true);
    }
}
