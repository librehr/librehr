<div class="pt-10">
    <div class="grid grid-cols-3 gap-4 mb-4">
        <x-filament::section class="p-0">
            <x-slot name="heading">
                Attendance
            </x-slot>
            <div class="flex flex-col rounded-lg py-4 divide-y gap-4 items-center justify-center">
                <div class="w-full flex flex-col gap-4 justify-center items-center min-h-[80px] ">
                    <div class="flex flex-col items-center justify-items-center gap-1 mb-2">
                <span class="text-lg font-semibold flex flex-row items-center justify-items-center gap-2">
                    @if ($currentAttendance && data_get($currentAttendance, 'end') === null)
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                    @endif
                    @persist('timer')
                        @livewire('attendance-today-summary', ['currentAttendance' => $currentAttendance])
                    @endpersist
                </span>
                        <span class="text-sm text-gray-700">Today Journey</span>
                    </div>
                    <x-filament::button color="{{ $currentAttendance && data_get($currentAttendance, 'end') === null  ? 'gray' : 'primary' }}" outlined wire:click="registerAttendanceNow">
                        {{ data_get($currentAttendance, 'start') !== null && data_get($currentAttendance, 'end') === null ? 'Finish' : 'Start' }}
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
        <x-filament::section>
            Requests
        </x-filament::section>
        <x-filament::section>
            Notifications
        </x-filament::section>
    </div>

    <div class="flex flex-col gap-4">
        @foreach($posts as $post)
            <x-filament::section>
                <x-slot name="heading">
                    {{ data_get($post, 'title') }}
                </x-slot>

                <x-slot name="headerEnd">
                    {{ data_get($post, 'created_at')->format('M N, Y') }}
                </x-slot>

                {!! data_get($post, 'body') !!}
            </x-filament::section>
        @endforeach
    </div>

</div>

