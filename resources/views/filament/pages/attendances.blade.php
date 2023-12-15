<x-filament-panels::page>
    <div class="grid grid-cols-2">
        <div class="flex flex-col">
            <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                {{ $this->getNavigationLabel() }}
            </h1>
            <p>
                {{ $this->getSubheading() }}
            </p>
        </div>
        <div class="flex flex-col border rounded-lg py-4 divide-y gap-4 items-center justify-center">
            <div class="w-full flex justify-center">
                Attendance
            </div>
            <div class="w-full flex flex-col gap-4 justify-center items-center min-h-[150px] ">
            <div class="flex flex-col items-center justify-items-center gap-1 mb-2">
                <span class="text-lg font-semibold flex flex-row items-center justify-items-center gap-2">
                    @if ($currentAttendance)
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                    @endif
                    @persist('timer')
                    @livewire('attendance-today-summary')
                    @endpersist
                </span>
                <span class="text-sm text-gray-700">Today Journey</span>
            </div>
                <x-filament::button color="{{ $currentAttendance ? 'gray' : 'primary' }}" outlined wire:click="registerAttendanceNow">
                    {{ $currentAttendance ? 'Finish' : 'Start' }}
                </x-filament::button>
            </div>
        </div>
    </div>

    <div class="flex flex-row border rounded-lg gap-4 divide-x justify-items-center items-center">
        <div class="p-4 flex flex-col">
            <span>40h</span>
            <span class="text-xs">Total</span>
        </div>
        <div class="p-4 flex flex-col">
            <span>120h</span>
            <span class="text-xs">Estimate</span>
        </div>
        <div class="flex-grow p-4 items-start h-full">
            Grafica
        </div>
        <div class="p-4 flex flex-col">
            <span>0h</span>
            <span class="text-xs">Extra</span>
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

        <x-filament::modal id="confirm-delete-attendance">
            Are you sure?

            <x-slot name="footerActions">
                <x-filament::button wire:click="confirmDeleteAttendance(true)">
                    Confirm
                </x-filament::button>
                <x-filament::button wire:click="confirmDeleteAttendance(false)">
                    Cancel
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

        @foreach($days as $day)
            <div class="flex flex-row justify-between col-span-10 w-full border-b {{ $day['date'] === date('Y-m-d') ? 'bg-orange-50' : '' }}">
                <div class="w-full flex flex-col col-span-3 p-4 py-6">
                    <span>{{ $day['number'] }} {{ $day['month_name']  }}</span>
                    <span class="text-sm text-gray-600">
                        {{ $day['day_name'] }}
                    </span>
                </div>
                <div class="flex-grow w-full flex flex-col col-span-3 p-4 gap-2">
                    @foreach($day['attendances'] as $attendance)
                        <div class="flex flex-row gap-4 items-baseline">
                            <input x-mask="99:99" value="{{ $attendance['startFormat'] }}" type="text" class="py-1 text-center text-lg rounded border border-gray-300 w-20">
                            <input x-mask="99:99" value="{{ $attendance['endFormat'] }}" type="text" class="py-1 text-center text-lg rounded border border-gray-300 w-20">
                            @if ($attendance['end'] === null)
                                <button>Finish</button>
                            @endif

                            @if ($attendance['end'] !== null)
                                <x-filament::button  color="primary" size="xs" outlined   wire:click="deleteAttendance({{ $attendance['id'] }})">
                                    Delete
                                </x-filament::button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="w-full  flex flex-col col-span-3 p-4">
                    <div class="flex flex-row gap-4 justify-end">
                        <span>
                            Worked
                        </span>
                        <span class="flex justify-end items-end min-w-[80px]">
                            {{ $day['total_seconds'] }}
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
