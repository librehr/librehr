<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }" class="text-sm border-b p-2">
        <!-- Interact with the `state` property in Alpine.js -->
        @php
            $item = data_get($this, $getStatePath());
        @endphp

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <span class="flex-grow">
                {{ $item['name'] }}
            </span>
                <span>
                {{ $item['size'] }}
            </span>
                <span>
                {{ $item['uploaded_at'] }}
            </span>
        </div>
    </div>
</x-dynamic-component>
