<x-filament-panels::page>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-5">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex flex-row gap-4">
                        {!! data_get($task, 'priorityIcon') !!}
                        {{ data_get($task, 'name') }}
                    </div>
                </x-slot>

                {{ data_get($task, 'description') }}
            </x-filament::section>
        </div>
        <div class="w-full col-span-7">
            <x-filament::section>
                <x-slot name="heading">
                    {{ \Illuminate\Support\Carbon::parse(data_get($task, 'start'))->format('d/m/Y') }} -
                    {{ \Illuminate\Support\Carbon::parse(data_get($task, 'end'))->format('d/m/Y') }}
                </x-slot>

                <x-slot name="headerEnd">
                    <span class="rounded font-black">
                        {{ array_search(data_get($task, 'status'), collect(\App\Enums\TaskStatusEnum::cases())->pluck('value','name')->toArray()) }}
                    </span>
                </x-slot>

                <div class="flex flex-col gap-4">
                @foreach(data_get($task, 'activities', []) as $activity)
                    <div>
                        {{ data_get($activity, 'attributes.body') }}
                    </div>
                @endforeach
                </div>

                <textarea class="border border-gray-300 rounded w-full min-h-[200px]" wire:model="bodyActivity"></textarea>
                <button class="bg-gray-300 p-2 rounded hover:bg-gray-200" wire:click="postMessage">Post Message</button>

            </x-filament::section>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
