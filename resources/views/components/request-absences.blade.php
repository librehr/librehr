@props([
    'request'
])
<div class="flex flex-row justify-between p-4 px-6 hover:bg-gray-100">
    <div class="flex">
        <div class="flex flex-col px-6">
            <div class="flex flex-col gap-1">
                <div class="text-xs mb-2"><span class="font-semibold text-gray-700">Absence Request</span> on  {{ data_get($request, 'requestable.created_at')->format('d/m/Y H:i') }}</div>
                <span>
                    New  "<span class="font-semibold underline">{{ data_get($request, 'requestable.absenceType.name') }}</span>"  requested from <b>{{ ucwords(data_get($request, 'contract.user.name')) }} </b>
                    @if(!empty(data_get($request, 'contract.team.name')))
                        ({{ data_get($request, 'contract.team.name') }})
                    @endif
                    <span>
                        request {{ \Carbon\Carbon::parse(data_get($request, 'requestable.end'))->diffInDays(data_get($request, 'requestable.start'))+1 }} days
                    </span>
                    <span class="font-semibold">{{ data_get($request, 'requestable.start')->format('d/m/Y') }}</span> to <span class="font-semibold">{{ data_get($request, 'requestable.end')->format('d/m/Y') }}</span></span>
            </div>
            @if(!empty(data_get($request, 'requestable.comments')))
            <div class="text-gray-700 text-sm flex flex-col mt-2">
                <span class="font-semibold text-xs">Comments from <b>{{ ucwords(data_get($request, 'contract.user.name')) }} </b></span>
                 <p class="pl-3 border-l-4 border-primary-300 mt-1">
                    {{ data_get($request, 'requestable.comments') }}
                </p>
            </div>
            @endif
        </div>
    </div>
    <div class="flex flex-row gap-2">
        {{  $this->getAction('time-off-action')
                     ->arguments(
                         [$request]
                     )
                     ->render() }}
    </div>
</div>
