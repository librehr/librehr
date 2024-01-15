@props([
    'request'
])
<div class="flex flex-row justify-between p-4 px-6 hover:bg-gray-100">
    <div class="flex">
        <div class="flex flex-col px-6">
            <div class="flex flex-col gap-1">
                <div class="text-xs mb-2"><span class="font-semibold text-gray-700">Absence Request</span> on  {{ data_get($request, 'requestable.created_at')->format('d/m/Y H:i') }}</div>
                <span>
                    <b>{{ ucwords(data_get($request, 'contract.user.name')) }} </b>
                    @if(!empty(data_get($request, 'contract.team.name')))
                        (from {{ data_get($request, 'contract.team.name') }})
                    @endif
                    <span>
                        request {{ \Carbon\Carbon::parse(data_get($request, 'requestable.end'))->diffInDays(data_get($request, 'requestable.start'))+1 }} days
                    </span>
                </span>
                <span>{{ data_get($request, 'requestable.start')->format('d/m/Y') }} hasta {{ data_get($request, 'requestable.end')->format('d/m/Y') }}</span>
            </div>
            @if(!empty(data_get($request, 'requestable.comments')))
            <span class="text-gray-700 text-sm flex flex-col mt-2">
                <span class="font-semibold text-xs">Comments from <b>{{ ucwords(data_get($request, 'contract.user.name')) }} </b></span>
                 <p class="pl-3 border-l-4 border-primary-300 mt-1">
                    {{ data_get($request, 'requestable.comments') }}
                </p>
            </span>
            @endif
        </div>
    </div>
    <div class="flex flex-row gap-2">
        <a href="{{ route(\App\Filament\Resources\AbsenceResource::getRouteBaseName('app') . '.view', data_get($request, 'requestable.id')) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-primary-500 hover:text-primary-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </a>
    </div>
</div>
