<x-filament-panels::page>
    <div class="flex flex-row border rounded-lg gap-4 divide-x justify-items-center items-center">
        <div class="p-4 flex flex-col">
            <span>40h</span>
            <span class="text-xs">Total<span>
        </div>
        <div class="p-4 flex flex-col">
            <span>120h</span>
            <span class="text-xs">Estimate<span>
        </div>
        <div class="flex-grow p-4 items-start h-full">
            Grafica
        </div>
        <div class="p-4 flex flex-col">
            <span>0h</span>
            <span class="text-xs">Extra<span>
        </div>
    </div>

    <div class="grid grid-cols-10 border rounded-lg    justify-items-center items-center">
        <div class="col-span-10 flex items-center py-4 border-b w-full justify-center">
            Status: currently in progress
        </div>
        <div class="flex flex-row justify-between col-span-10 w-full bg-gray-200">
            <div class="w-full flex flex-col col-span-3 p-4">
                <span>Day</span>
            </div>
            <div class="flex-grow w-full flex flex-col col-span-3 p-4">
                <span>Work</span>
            </div>
            <div class="w-full col-span-3 flex flex-col p-4 items-end justify-end">
                <span>Hours</span>
            </div>
        </div>
        @foreach(range(1,$selected->daysInMonth) as $day)
            <div class="flex flex-row justify-between col-span-10 w-full border-b">
                <div class="w-full flex flex-col col-span-3 p-4">
                    <span>{{ $day }} {{ str($selected->format('M'))->lower()}}.</span>
                    <span class="text-sm text-gray-600">
                        {{ str(\Carbon\Carbon::create($selected->format('Y'), $selected->format('m'), $day)->format('l'))->lower() }}
                    </span>
                </div>
                <div class="flex-grow w-full flex flex-col col-span-3 p-4">
                    <x-filament::input.wrapper>
                        <x-filament::input
                                type="text"
                                wire:model="day{{ $day }}"
                        />
                    </x-filament::input.wrapper>
                </div>
                <div class="w-full  flex flex-col col-span-3 p-4">
                    <div class="flex flex-row gap-4 justify-end">
                        <span>
                            Worked
                        </span>
                        <span class="flex justify-end items-end min-w-[80px]">
                            0.00h
                        </span>
                    </div>
                    <div class="flex flex-row gap-4 justify-end">
                        <span>
                            Estimated
                        </span>
                        <span class="flex justify-end items-end min-w-[80px]">
                            0.00h
                        </span>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</x-filament-panels::page>
