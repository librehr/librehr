<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}'), document: undefined }">
        @php($documents = data_get($getRecord(), 'documents', []))
        <div class="grid grid-cols-6 text-sm items-center gap-4 border rounded-lg px-3 py-4">
            @forelse($documents as $document)
                <span class="col-span-3 cursor-pointer font-semibold text-primary-600 hover:underline" @click="document = {{ $document->id }}; $dispatch('open-modal', { id: 'show-document' })">
                    {{ $document->name }}
                </span>
                <span class="col-span-1 flex justify-end">
                    {{ \Illuminate\Support\Number::fileSize($document->size) }}
                </span>
                <span class="col-span-1 flex justify-end">
                    {{ $document->type }}
                </span>
                <span class="col-span-1 flex justify-end">
                    <x-filament::button size="xs" wire:click="openNewUserModal">
                        Delete
                    </x-filament::button>
                </span>
            @empty
                No Documents.
            @endforelse
        </div>
        <x-filament::modal id="show-document">
            {{-- Modal content --}}
            <div x-text="document"></div>
        </x-filament::modal>

    </div>
</x-dynamic-component>
