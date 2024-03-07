@props([
    'request'
])
<div class="flex flex-row justify-between p-4 px-6 hover:bg-gray-100">
    <div class="flex">
        <div class="flex flex-col px-6">
            <div class="flex flex-col gap-1">
                <div class="text-xs mb-2"><span class="font-semibold text-gray-700">File needs your signature</span> on  {{ data_get($request, 'requestable.created_at')?->format('d/m/Y H:i') }}</div>
                <span>You need to sign a file {{ data_get($request, 'requestable.date')?->format('Y-m') }}</span>
             </div>
        </div>
    </div>
    <div class="flex flex-row gap-2">
        {{  $this->getAction('signs')
              ->arguments(
                         [$request]
                     )
                     ->render() }}
    </div>
</div>
