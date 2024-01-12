<x-filament-panels::page>
    <div class="flex justify-end">
        <x-filament::button wire:click="dispatch('open-modal', { id: 'request-absence' })" icon="heroicon-m-plus" outlined>
            Request absence
        </x-filament::button>

        <x-filament::modal id="request-absence" slide-over>
            <div class="flex flex-col gap-2">
                <label for="type" class="font-semibold">Type</label>
                <select wire:model="absenceTypeId" id="type" class="border border-gray-300 rounded-lg">
                    <option selected></option>
                    @foreach ($type as $id => $typed)
                        <option value="{{ $id }}">
                            {{ $typed }}
                        </option>
                    @endforeach
                </select>
                <x-date-picker :startDate="$startDate"/>

                <label for="comments" class="font-semibold">Comments</label>
                <textarea wire:model="comments" id="comments" class="border border-gray-300 rounded-lg"></textarea>

                @if (session()->has('days'))
                    <div>
                        {{ session('days') }}
                    </div>
                @endif

                @if (session()->has('overlap'))
                    <div>
                        {{ session('overlap') }}
                    </div>
                @endif
                {{ json_encode($errors->any()) }}
                <x-filament::button :disabled="empty($startDate) || empty($endDate) || $errors->any() ? 'disabled' : false" wire:click="submitRequestTimeOff">
                    Submit Request
                </x-filament::button>
            </div>
        </x-filament::modal>


        <x-filament::modal id="view-absence" slide-over>
            <div class="flex flex-col gap-2">

                <x-filament::button :disabled="empty($startDate) || empty($endDate) || $errors->any() ? 'disabled' : false" wire:click="submitRequestTimeOff">
                    Submit Request
                </x-filament::button>
            </div>
        </x-filament::modal>
    </div>

    <div class="grid grid-cols-2 gap-8">
        <div class="flex flex-col gap-4">


            <x-time-off-calendar-component
                title="Pending"
                description="Pending absences that need to be validated by your team manager"
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </x-slot>
                <x-time-off-calendar-row-component :absences="data_get($contractAbsences, 'pending', [])"/>
            </x-time-off-calendar-component>

            <x-time-off-calendar-component
                title="Incoming Absences"
                description="This absences has been already aproved, you are not allowed to edit them."
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                    </x-slot>
                    <x-time-off-calendar-row-component :absences="data_get($contractAbsences, 'accepted', [])" status="incoming"/>
            </x-time-off-calendar-component>


            <x-time-off-calendar-component
                title="Past Absences"
                description="This absences has been already aproved, you are not allowed to edit them."
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    </x-slot>
                    <x-time-off-calendar-row-component :absences="data_get($contractAbsences, 'accepted', [])" status="past"/>
            </x-time-off-calendar-component>

            <x-time-off-calendar-component
                title="Denied"
                description="Denied absences are not editable."
            >
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    </x-slot>
                    <x-time-off-calendar-row-component :absences="data_get($contractAbsences, 'denied', [])"/>
            </x-time-off-calendar-component>


        </div>
        <div class="bg-gray-100 h-full p-4">
            <ul class="p-4 border-b mb-4 flex flex-row justify-center items-center gap-8">
                <button wire:click="updateYear('previous')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                    </svg>
                </button>
                <div class="min-w-[160px] text-center">
                    {{ $year }}
                </div>

                <button wire:click="updateYear('next')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                    </svg>
                </button>
            </ul>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                @foreach($calendar as $month)
                    <div class="flex flex-col">
                        <span class="text-gray-400 mb-2">
                            {{ str($month['name'])->lower() }}
                        </span>
                        <div class="grid grid-cols-7">
                            @foreach(['m','t','w','t','f','s','s'] as $dayN)
                                <span class="text-xs text-gray-500 text-center">
                                    {{ $dayN }}
                                </span>
                            @endforeach
                            @foreach ($month['weeks'] as $day)
                                @foreach($day as $key => $weekDay)
                                    @php
                                        if (!empty(data_get($weekDay, 'events.absences.*', []))) {
                                            $backgroundColor = data_get($weekDay, 'events.absences.*.absenceType.attributes.color.background', []);
                                            $textColor = data_get($weekDay, 'events.absences.*.absenceType.attributes.color.text', []);
                                        }
                                    @endphp

                                    @php($toolTip = implode(', ', data_get($weekDay, 'events.tooltip', [])))
                                    <div style="@if(!empty(data_get($weekDay, 'events.absences', [])))
                                        background-color:{{ head($backgroundColor) }}; color: {{ head($textColor) }}
                                    @endif" class="flex flex-row items-center p-1.5 justify-items-center text-xs


                                    @if(data_get($weekDay, 'date') == now()->format('Y-m-d'))
                                        shadow-inner shadow-black
                                    @endif
                                    "

                                    >
                                        @if (!empty($weekDay))
                                            <div class="text-center w-full
                                             @if(!empty(data_get($weekDay, 'events.holiday', [])))
                                                   ring-2 ring-primary-600
                                             @endif
                                            "
                                                 @if(!empty($toolTip))
                                                     x-data x-tooltip-span.top="{{ $toolTip }}"
                                                @endif
                                                 >{{ $weekDay['number'] }}</div>

                                        @endif


                                    </div>
                                @endforeach

                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>


</x-filament-panels::page>
