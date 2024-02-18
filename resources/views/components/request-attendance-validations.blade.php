@props([
    'request'
])
<div class="flex flex-row justify-between p-4 px-6 hover:bg-gray-100">
    <div class="flex">
        <div class="flex flex-col px-6">
            <div class="flex flex-col gap-1">
                <div class="text-xs mb-2"><span class="font-semibold text-gray-700">Attendance Validation Request</span> on  {{ data_get($request, 'requestable.created_at')->format('d/m/Y H:i') }}</div>
                <span>Please, make a revision to the attendance. {{ data_get($request, 'requestable.date')->format('Y-m') }}</span>

                <a class="text-primary-600" href="{{ route('filament.app.pages.attendances', ['date' => data_get($request, 'requestable.date')]) }}">Go to Attendances</a>
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
        {{  $this->getAction('validate-attendances')
                     ->arguments(
                         [$request]
                     )
                     ->render() }}
    </div>
</div>
