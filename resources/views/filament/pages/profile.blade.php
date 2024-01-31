<x-filament-panels::page>
    <form wire:submit="edit" class="flex flex-col gap-4">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">
                Update
            </x-filament::button>
        </div>

    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
