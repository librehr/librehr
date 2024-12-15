@props([
    'absences',
    'status',
])

@php
 if (isset($status) && $status === 'incoming') {
     $absences = collect($absences)->where('start', '>', now())->toArray();
 } elseif (isset($status) && $status === 'past') {
     $absences = collect($absences)->where('start', '<', now())->toArray();
 }
@endphp

@empty($absences)
    <div class="text-gray-600 mb-4">
        There are not absences yet.
    </div>
@else
    <div class="mb-8 flex flex-col divide-gray-200 gap-2">
        @foreach ($absences as $absence)
            <div wire:click="mountAction('cant-edit', [{{ $absence['id'] }}])"  class="p-6 flex flex-row gap-4 cursor-pointer bg-white border rounded-2xl hover:bg-gray-100">
                <div class="flex flex-row min-w-[70px] gap-2">
                    <span class="rounded-2xl border-2 p-1 flex flex-col items-center justify-items-center">
                        <span class="text-xs text-primary-600 font-semibold px-2">
                            {{ str(\Carbon\Carbon::parse($absence['start'])->format('M'))->upper() }}
                        </span>
                        <span class="text-xl font-bold">
                            {{ \Carbon\Carbon::parse($absence['start'])->format('d') }}
                        </span>
                    </span>
                    @if($absence['DiffInDays'] > 1)
                        <span class="flex items-center">-></span>
                        <span class="rounded-2xl border-2 p-1 flex flex-col items-center justify-items-center">
                    <span class="text-xs text-primary-600 font-semibold px-2">
                            {{ str(\Carbon\Carbon::parse($absence['end'])->format('M'))->upper() }}
                    </span>
                    <span class="text-xl font-bold">
                            {{ \Carbon\Carbon::parse($absence['end'])->format('d') }}
                    </span>
                </span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">
                        {{ data_get($absence, 'absence_type.name') }}
                    </span>
                    <span class="text-gray-600">
                    {{ $absence['DiffInDays'] }} day{{ $absence['DiffInDays'] > 1 ? 's' : '' }} ({{ \Carbon\Carbon::parse($absence['start'])->format('Y') }})
                </span>
                </div>
            </div>
        @endforeach
    </div>
@endif
