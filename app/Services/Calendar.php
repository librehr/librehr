<?php

namespace App\Services;


use App\Models\Absence;
use Illuminate\Support\Carbon;

class Calendar extends BaseService
{
    public function buildCalendar($year, $contracts = [])
    {
        $date = Carbon::createFromDate($year);
        $startOfCalendar = $date->copy()->firstOfYear();
        $endOfCalendar = $date->copy()->lastOfYear();

        $calendar = \App\Models\Calendar::query()
            ->whereYear('date', $year)
            ->get()
            ->groupBy('date')
        ->mapWithKeys(function ($row, $key) {
            return [Carbon::createFromDate($key)->format('Y-m-d') => $row];
        })->toArray();

        // TODO: add year and complete.
        $absences = Absence::query()
            ->whereIn('contract_id', $contracts)
            ->whereYear('start', $year)
            ->where('status', 'pending')
            ->orderBy('start')
            ->get();


        $date = [];
        while($startOfCalendar <= $endOfCalendar) {
            $holidays = data_get($calendar, $startOfCalendar->format('Y-m-d'), []);
            $absenceses = [];
            foreach ($absences as $absence) {
                if ($startOfCalendar->between(Carbon::parse($absence->start), Carbon::parse($absence->end))) {
                    $absenceses[] = $absence;
                }
            }

            $toolTip = data_get($holidays, '*.name', []);
            $toolTip = [...data_get($absenceses, '*.absenceType.name', []), ...$toolTip];


            $date[$startOfCalendar->format('n')]['weeks']
            [$startOfCalendar->format('W')]
            [$startOfCalendar->format('N')] =
                [
                    'day_long' => $startOfCalendar->format('l'),
                    'day_short' => $startOfCalendar->format('D'),
                    'day_number' => $startOfCalendar->format('N'),
                    'day' => str($startOfCalendar->format('D'))->lower()->substr(0, -2),
                    'date' => $startOfCalendar->format('Y-m-d'),
                    'number' => $startOfCalendar->format('j'),
                    'events' => [
                        'holiday' => $holidays,
                        'absences' => $absenceses,
                        'tooltip' => $toolTip
                    ]
                ];

            $date[$startOfCalendar->format('n')]['name'] = $startOfCalendar->format('F');
            $date[$startOfCalendar->format('n')]['month'] = $startOfCalendar->format('m');
            $date[$startOfCalendar->format('n')]['year'] = $startOfCalendar->format('Y');
            $startOfCalendar->addDay();
        }

        return $this->fillEmptyDaysInWeeks($date);
    }

    protected function fillEmptyDaysInWeeks($date)
    {
        foreach ($date as $monthKey => $month) {
            foreach ($month['weeks'] as $weekDay => $day) {
                $keysToAdd = array_diff(range(1, 7), array_keys($day));

                foreach ($keysToAdd as $addNumber) {
                    $date[$monthKey]['weeks'][$weekDay][$addNumber] = [];
                }

                ksort($date[$monthKey]['weeks'][$weekDay]);
            }
        }

        return $date;
    }
}
