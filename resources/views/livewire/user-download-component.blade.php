<div>
    @empty(!$documents)
        <div class="grid grid-cols-2 gap-6" >
            @foreach ($documents as $category => $documents)
                <div wire:click="mountAction('category', @js($documents))" class="flex justify-between w-full border rounded-lg bg-white px-6 py-3 items-center cursor-pointer hover:bg-gray-50">
                    {{ $category }} <span class="bg-gray-100 p-2 text-xs rounded">{{ count($documents) }}</span>
                </div>
            @endforeach
        </div>
    @else
        <x-filament::section class="text-sm">
            No documents uploaded.
        </x-filament::section>
    @endempty
        <x-filament-actions::modals />
</div>
