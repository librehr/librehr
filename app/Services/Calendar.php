<?php

namespace App\Services;


use App\Models\Absence;
use App\Models\Contract;
use Illuminate\Support\Carbon;

class Calendar extends BaseService
{
    public function buildCalendar($contractId, $year, $absences = [])
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

        $date = [];
        $totalAbsences = [];
        while($startOfCalendar <= $endOfCalendar) {
            $holidays = data_get($calendar, $startOfCalendar->format('Y-m-d'), []);
            $absenceses = [];
            foreach ($absences as $absence) {
                if ($startOfCalendar->between(Carbon::parse($absence->start), Carbon::parse($absence->end))) {
                    $absenceses[] = $absence;
                    $totalAbsences[] = $absence;
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

        $date = $this->fillEmptyDaysInWeeks($date);
        $summary = $this->getSummaryByContract($contractId, $totalAbsences);

        return [$date, $summary];
    }

    protected function getSummaryByContract($contractId, $totalAbsences)
    {
        $contract = Contract::query()->with('contractType')->find($contractId);
        $totalDays = data_get(
            $contract,
            'contractType.attributes.vacations',
            config('librehr.default_vacations')
        );

        $allowedAbsences = count(data_get($totalAbsences, '*.allowed', []));
        return [
            'total_days' => $totalDays,
            'total_days_selected' => $allowedAbsences,
            'total_days_pending' => ($totalDays-$allowedAbsences)
        ];
    }

    public function getOverlaps($contractId, $start, $end)
    {
        $contract = Contract::query()->find($contractId);
        $absences = Absence::query()
            ->with(['contract', 'contract.user', 'contract.team'])
            ->whereBetween('start', [$start, $end])
            ->whereBetween('end', [$start, $end])
            ->get()
            ->groupBy('contract.team.id');

        $business = $absences->collapse()
            ->where('contract.team.id', '!=', data_get($contract, 'team_id', 0))
            ->groupBy('contract.user.name')->toArray();

        $myTeam = collect(data_get($absences, data_get($contract, 'team_id'), []))
            ->groupBy('contract.user.name')
            ->toArray();

        return [
            'business' => $business,
            'team' => $myTeam,
        ];
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
