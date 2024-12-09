<x-filament-panels::page>
    <ul class="-mt-6 p-6 border-b flex flex-row justify-center items-center gap-8">
        <a wire:click="previous" class="cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
            </svg>
        </a>
        <div class="min-w-[160px] text-center">
            {{ $selected->format('F, Y') }}
        </div>
        @if($selected < now()->startOfMonth())
            <a wire:click="next" class="cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                </svg>
            </a>
        @else
            <span class="w-6 h-6"></span>
            @endif

    </ul>
    <div class="grid grid-cols-2">
        <div class="flex flex-col">
            <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                {{ $this->getNavigationLabel() }}
            </h1>
            <p class="my-4">
                {{ $this->getSubheading() }}
            </p>
        </div>
        <div class="flex flex-col border bg-white rounded-lg py-4 divide-y gap-4 items-center justify-center">
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
                    @livewire('attendance-today-summary', ['currentAttendance' => $currentAttendance])
                </span>
                <span class="text-sm text-gray-700 flex flex-row gap-1 items-center">
                     <span>
                         {{ now()->format('d \o\f F, Y') }}
                     </span>
                </span>
            </div>
                <x-filament::button color="{{ $currentAttendance ? 'gray' : 'primary' }}" outlined wire:click="registerAttendanceNow">
                    {{ data_get($currentAttendance, 'start') !== null ? 'Finish' : 'Start' }}
                </x-filament::button>
            </div>
        </div>
    </div>

    <div class="flex flex-row border rounded-lg gap-0 bg-white justify-items-center items-center">
        <div class="p-4 flex flex-col">
            <span>{{ data_get($summary, $contractId . '.total_time') }}</span>
            <span class="text-xs">Total</span>
        </div>
        <div class="p-4 flex flex-col">
            <span>{{ data_get($summary, $contractId . '.total_time_estimated') }}</span>
            <span class="text-xs">Estimate</span>
        </div>
        <div class="flex-grow p-4">
                @livewire(App\Filament\App\Widgets\AttendancesChart::class)
        </div>
        <div class="p-4 flex flex-col">
            <span>{{ data_get($summary, $contractId . '.total_time_extra') }}</span>
            <span class="text-xs">Extra</span>
        </div>
    </div>

    <div class="grid grid-cols-10 border rounded-lg bg-white divide-y">
        <div class="col-span-10 text-center py-6">
            Status:
            @if(data_get($summary, $contractId . '.user.validations') !== null)
                <span class="bg-success-500 text-sm text-white p-2 rounded">Validated</span>
            @else
                @if($selected->endOfMonth() < now())
                    <span class="bg-warning-500 text-sm text-white p-2 rounded">pending approval</span>
                @else
                    <span class="bg-primary-500 text-sm animate-pulse text-white p-2 rounded">currently in progress</span>
                @endif
            @endif

        </div>

        <div class="col-span-2 p-4 bg-gray-200">
            <span>Day</span>
        </div>

        <div class="col-span-6 p-4 bg-gray-200">
            <span>Work</span>
        </div>

        <div class="col-span-2 p-4 bg-gray-200">
            <span>Hours</span>
        </div>

        @foreach(data_get($days, $contractId) as $day)
            @php($dayError = data_get($day, 'errors', false === true) ? 'bg-warning-50' : null)
            <div class="col-span-2 p-4 {{ $dayError }} ">
                <div class="w-full flex flex-col col-span-3 p-4 py-6">
                    <span>{{ $day['number'] }} {{ $day['month_name']  }}</span>
                    <span class="text-sm text-gray-600">
                        {{ $day['day_name'] }}
                    </span>
                </div>
            </div>

            <div class="col-span-6 p-4 {{ $dayError }}">
                <div class="flex-grow w-full flex flex-col col-span-3 p-4 gap-2">
                    <div class="flex flex-row gap-4">
                    @foreach(data_get($day, 'calendar', []) as $calendar)
                        <span class="{{ $calendar->workable == true ? 'bg-success-300' : 'bg-warning-200' }} px-3 py-1 rounded-lg text-xs flex flex-row gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>

                            {{ data_get($calendar, 'name') }}
                        </span>
                    @endforeach
                    </div>

                    <div class="flex flex-row gap-4">
                        @foreach(data_get($day, 'absences', []) as $absence)
                            <span class="{{ data_get($absence, 'absence_type.attributes.is_holidays') == true ? 'bg-success-300' : 'bg-warning-200' }} px-3 py-1 rounded-lg text-xs flex flex-row gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>
                            {{ data_get($absence, 'absence_type.name') }}
                        </span>
                        @endforeach                    </div>

                    @foreach($day['attendances'] as $attendance)
                        <div class="flex flex-row gap-4 items-baseline">

                            <x-filament::icon-button
                                icon="heroicon-m-information-circle"
                                color="gray"
                                tooltip="Created manually"
                            />

                            <input id="{{ $attendance['id'] }}_start"   wire:change="updateAttendance({{ $attendance['id'] }}, 'start', document.getElementById('{{ $attendance['id'] }}_start').value)" value="{{ $attendance['startFormat'] }}" type="time" class="py-1 text-center text-lg rounded border border-gray-300" />
                            <input id="{{ $attendance['id'] }}_end"  wire:change="updateAttendance({{ $attendance['id'] }}, 'end', document.getElementById('{{ $attendance['id'] }}_end').value)" value="{{ $attendance['endFormat'] }}" type="time" class="py-1 text-center text-lg rounded border border-gray-300" />

                            <div class="flex items-center">
                                {{  $this->getAction('delete_time_action')
                     ->arguments([
                         'id' => $attendance['id']
                     ])
                     ->render() }}
                            </div>


                        </div>
                    @endforeach

                    @if(now()->timestamp > strtotime($day['date']))
                        <span>
                            {{  $this->getAction('add_time_action')
                                ->arguments([
                                    'date' => $day['date']
                                ])
                                ->render() }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-span-2 p-4 {{ $dayError }}">
                <div class="w-full  flex flex-col col-span-3 p-4">
                    <div class="flex flex-row gap-4 justify-end">
                        <span>
                            Worked
                        </span>
                        <span class="flex justify-end items-end min-w-[80px]">
                            {{ $day['total_time'] }}
                        </span>
                    </div>
                    <div class="flex flex-row gap-4 justify-end">
                        <span>
                            Estimated
                        </span>
                        <span class="flex justify-end items-end min-w-[80px]">
                            {{ $day['total_time_estimated'] }}
                        </span>
                    </div>
                    @if (data_get($day, 'total_time_extra'))
                        <div class="flex flex-row gap-4 justify-end">
                        <span class="font-semibold">
                            Extra
                        </span>
                            <span class="flex justify-end items-end min-w-[80px] text-primary-600">
                            {{ $day['total_time_extra'] }}
                        </span>
                        </div>
                    @endif

                    @if($dayError !== null)
                        <div class="flex flex-row gap-4 w-full justify-end">
                        <span class="font-semibold text-danger-600">
                            warning
                        </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</x-filament-panels::page>
