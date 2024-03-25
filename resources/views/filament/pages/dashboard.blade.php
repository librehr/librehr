<div class="pt-10">
    @if($contractId && $businessId)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 text-sm">
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
                        <span class="text-sm text-gray-700 flex flex-row gap-1 items-center">
                     <span>
                         {{ now()->format('d \o\f F, Y') }}
                     </span>
                </span>
                    </div>
                    <x-filament::button color="{{ $currentAttendance && data_get($currentAttendance, 'end') === null  ? 'gray' : 'primary' }}" outlined wire:click="registerAttendanceNow">
                        {{ data_get($currentAttendance, 'start') !== null && data_get($currentAttendance, 'end') === null ? 'Finish' : 'Start' }}
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section class="col-span-2">
            <x-slot name="heading">
                Notifications / Requests
            </x-slot>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col">
                    <div class="font-semibold text-gray-700">
                        Notifications
                    </div>
                    <div wire:poll.5s="getNotifications">
                    @if ($notificationsCount > 0)
                            <x-filament::button  class="mt-2" x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
                            >
                                <x-slot name="badge">
                                    {{ $notificationsCount }}
                                </x-slot>
                                <x-slot name="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 animate-pulse">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                                    </svg>
                                </x-slot>
                                Open Notifications
                            </x-filament::button>
                        @else
                            <span class="text-gray-600">You don't have any notification pending.</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="font-semibold text-gray-700">
                        Watch out the requests!
                    </div>
                    <div>
                        @if ($requestsCount > 0)
                            <x-filament::button class="mt-2" size="lg" wire:click="goToRequests">
                                <x-slot name="badge">
                                    {{ $requestsCount }}
                                </x-slot>
                                <x-slot name="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 animate-pulse">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                </x-slot>

                                Watch out
                            </x-filament::button>
                        @else
                            <span class="text-gray-600">You don't have any request pending.</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Office Desk Booking
            </x-slot>

            <div class="mb-4 flex flex-col gap-4">
                @if ($todayBooked)
                    <span class="text-2xl text-green-600">Seat {{ data_get($todayBooked, 'desk.name') }}</span>
                    <span class="-mt-4 text-xs text-gray-500">This is the seat your reserved for today!</span>
                    <span>You have booked seat <span class="text-green-700 font-black">{{ data_get($todayBooked, 'desk.name') }}</span> for today in <b>{{ data_get($todayBooked, 'desk.room.floor.place.name') }}</b>, room <b>{{ data_get($todayBooked, 'desk.room.name') }}</b> @ the <b>{{ data_get($todayBooked, 'desk.room.floor.name') }}</b></span>

                    <span>
                    <x-filament::button color="gray" wire:click="goToDeskBookings( {{ data_get($todayBooked, 'desk.room.id') }}, '{{ data_get($todayBooked, 'start') }}')">
                        View in map
                    </x-filament::button>
                    </span>
                @else
                    Do you want to come to the office today? Get excited and reserve your spot!

                    <x-filament::button wire:click="goToDeskBookings">
                        Book now!
                    </x-filament::button>
                @endif
            </div>
        </x-filament::section>

        <x-filament::section class="p-0 col-span-2">
            <x-slot name="heading">
                {{ data_get($user->getActiveBusiness(), 'name') }} Calendar
            </x-slot>
            <div class="flex flex-col divide-y">
                @forelse($calendar as $day)
                    <div class="py-2 flex justify-between">
                        {{ data_get($day, 'date')->format('F, d') }}
                        @if(data_get($day, 'workable', false) === false)
                            <span class="text-xs rounded-lg text-primary-600 bg-primary-100 px-2">
                                Festive
                            </span>
                        @endif
                        <span class="px-2">
                            {{ data_get($day, 'name') }}
                        </span>
                    </div>
                @empty
                    Nothing registered.
                @endforelse
            </div>
        </x-filament::section>
    </div>

    @endif
    <div class="flex flex-col gap-4 mb-4">
        @foreach($posts as $post)
            <x-filament::section>
                <x-slot name="heading">
                    {{ data_get($post, 'title') }}
                </x-slot>

                <x-slot name="headerEnd">
                    {{ data_get($post, 'created_at')->format('M N, Y') }}
                </x-slot>

                <div class="blog-format">
                    {!! data_get($post, 'body') !!}
                </div>

            </x-filament::section>
        @endforeach
    </div>

</div>

