<x-filament-panels::page>
    @if ($todayBooked)

        <x-filament::section class="bg-yellow-50">
            <div>
                <span>You have booked seat <span class="text-green-700 font-black">{{ data_get($todayBooked, 'desk.name') }}</span> for {{ data_get($todayBooked, 'start') == now()->format('d/m/Y') ? 'today' : data_get($todayBooked, 'start')->format('d/m/Y') }} in <b>{{ data_get($todayBooked, 'desk.room.floor.place.name') }}</b>, room <b>{{ data_get($todayBooked, 'desk.room.name') }}</b> @ the <b>{{ data_get($todayBooked, 'desk.room.floor.name') }}</b></span>
                <span>
                    <x-filament::button color="gray" wire:click="goToDeskBookings( {{ data_get($todayBooked, 'desk.room.id') }})">
                        View in map
                    </x-filament::button>
                    </span>
            </div>
        </x-filament::section>


    @endif
    <div class="flex flex-row gap-4">
        <x-filament::input.wrapper class="w-full">
            <div class="flex flex-col p-1">
                <label for="place" class="mx-2 text-xs text-gray-500">Date</label>
                <input type="date" wire:model="date" wire:change="resetAll()" value="{{ $date }}" class="border-0 focus:ring-0 outline-none blur-none" />
            </div>
        </x-filament::input.wrapper>
        <x-filament::input.wrapper class="w-full">
            <label for="place" class="mx-2 text-xs text-gray-500">Place</label>
            <x-filament::input.select wire:model="places">
                    <option value="{{ data_get($places, 'id') }}">{{ data_get($places, 'name') }}</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
        <x-filament::input.wrapper class="w-full">
            <label for="place" class="mx-2 text-xs text-gray-500">Floor</label>
            <x-filament::input.select wire:model.change="floor">
                <option></option>
                @foreach($floors as $floor)
                    <option value="{{ data_get($floor, 'id') }}">{{ data_get($floor, 'name') }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper class="w-full">
            <label for="place" class="mx-2 text-xs text-gray-500">Room</label>
            <x-filament::input.select wire:model.change="room">
                <option></option>
                @foreach($rooms as $room)
                    <option value="{{ data_get($room, 'id') }}">{{ data_get($room, 'name') }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

    </div>
    @if($record)
        <x-filament::section>
            <div class="flex items-center justify-center" style=" background-image: url('{{ asset('images/grid.webp') }}');">
                <x-room-map-component :record="$record" :bookings="true" :selected="$selected ?? now()" />
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
