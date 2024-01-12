<x-filament-panels::page>
    <button
            class=""
            x-data="{}"
            x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
            type="button"
    >
        Notifications
    </button>
    <div class="container mx-auto max-w-4xl flex flex-col border rounded-lg divide-y gap-4">
        @foreach($requests as $request)

            @if (data_get($request, 'request.name') == 'absences')
                <div class="flex flex-row gap-4 justify-between p-4 px-6">
                    <div class="flex gap-4">
                        <div class="flex flex-col px-6 gap-2">
            <div class="font-semibold flex flex-col">
                <small>Solicitud de ausencia</small>
                <span>De {{ data_get($request, 'contract.user.name') }}</span>
                <span>Desde {{ data_get($request, 'requestable.start') }} hasta {{ data_get($request, 'requestable.end') }}</span>
            </div>
                            <span class="text-gray-700 text-sm">
                 {{ data_get($request, 'requestable.created_at') }}
            </span>
                            <span class="text-gray-700 text-sm">
                 {{ data_get($request, 'data.body') }}
            </span>
                        </div>
                    </div>
                    <div class="flex flex-row gap-2">
                        <a href="{{ route(\App\Filament\Resources\AbsenceResource::getRouteBaseName('app') . '.edit', data_get($request, 'requestable.id')) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>

                        <a href="{{ route(\App\Filament\Resources\AbsenceResource::getRouteBaseName('app') . '.edit', data_get($request, 'requestable.id')) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

        @endforeach
    </div>
</x-filament-panels::page>
