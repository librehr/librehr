@props([
    'calendar',
    'months',
    'smColumns',
    'xsColumns',
])
@php
    if (!empty($months)) {
        $calendar = collect($calendar)->mapWithKeys(function ($value, $key) use ($months) {
            if (!empty($months) && in_array($key, $months)) {
                return [$key => $value];
            }

            return [];
        })->filter()->toArray();
    }

    $xsColumns = $xsColumns !== null ? $xsColumns : 1;
    $smColumns = $smColumns !== null ? $smColumns : 2;
@endphp

<div class="grid grid-cols-{{ $xsColumns }} sm:grid-cols-{{ $smColumns }} gap-8">
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
