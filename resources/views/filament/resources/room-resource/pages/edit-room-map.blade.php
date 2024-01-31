<x-filament-panels::page>
    <x-filament::section>
        <div class="flex items-center justify-center" style=" background-image: url('{{ asset('images/grid.webp') }}');">
            <x-room-map-component :record="$record" :bookings="false" :selected="now()"></x-room-map-component>
        </div>
    </x-filament::section>
</x-filament-panels::page>
