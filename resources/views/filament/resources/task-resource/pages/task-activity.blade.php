<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex flex-row gap-4">
                {!! data_get($task, 'priorityIcon') !!}

                {{ data_get($task, 'name') }}
            </div>

        </x-slot>

        <x-slot name="headerEnd">
            <span class="bg-gray-200 p-1 rounded text-sm">
                {{ array_search(data_get($task, 'status'), collect(\App\Enums\TaskStatusEnum::cases())->pluck('value','name')->toArray()) }}
            </span>
        </x-slot>


        {{ data_get($task, 'description') }}
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Activity
        </x-slot>

        <x-slot name="headerEnd">
            {{-- Input to select the user's ID --}}
        </x-slot>


    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
