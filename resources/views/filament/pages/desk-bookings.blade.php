<x-filament-panels::page>
    <div class="flex flex-row gap-4">
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
