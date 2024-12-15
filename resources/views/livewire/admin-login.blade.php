<div class="ml-3 text-sm">
    @if($admin)
        @if($adminPanel)
            <a class="border border-gray-200 hover:bg-gray-100 hover:border-gray-200 px-4 py-2 rounded-2xl" wire:navigate href="{{ route('filament.app.pages.dashboard', \App\Models\Business::query()->first()->id) }}">View App</a>
        @else
            <a class="border border-gray-200 hover:bg-gray-100 hover:border-gray-200 px-4 py-2 rounded-2xl" wire:navigate href="{{ route('filament.admin.pages.dashboard') }}">Go to Admin</a>
        @endif
    @endif
</div>
