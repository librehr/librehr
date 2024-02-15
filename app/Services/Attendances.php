<?php

namespace App\Services;


use App\Models\Attendance;
use App\Models\AttendanceValidation;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;

class Attendances extends BaseService
{
    public function getCurrentAttendance($contractId)
    {
        // Check if there is something openm just close it
        return Attendance::query()
            ->where('contract_id', $contractId)
            ->whereNull('end')
            ->whereNotNull('start')
            ->first();
    }

    public function startResumeAttendanceNow(
        $contractId,
        $attendanceId = null,
        $type = null,
        $date = null,
        $value = null,
        $start = null,
        $end = null
    )
    {
        if ($attendanceId === null && $type === 'new') {
            $start = explode(':', $start);
            $end = explode(':', $end);
            $attendance = Attendance::query()->create(
                [
                    'contract_id' => $contractId,
                    'date' => $date,
                    'start' => Carbon::createFromDate($date)->setTime($start[0], $start[1]),
                    'end' => Carbon::createFromDate($date)->setTime($end[0], $end[1]),
                ]
            );

            return $attendance;
        }

        if ($attendanceId && $type !== null) {
            $attendance = Attendance::query()
                ->where('contract_id', $contractId)
                ->where('id', $attendanceId)
                ->first();

            $time = explode(':', $value);
            $dateTime = Carbon::createFromDate( $attendance->date)
                ->setTime($time[0], $time[1])
                ->setTimezone(config('app.timezone'));

            if ($type === 'end') {
                $attendance->end = $dateTime;
            } else {
                $attendance->start = $dateTime;
            }

            $attendance->save();
            return $attendance;
        }

        $attendance = $this->getCurrentAttendance($contractId);
        if ($attendance) {
            $attendance->end = now()->setTimezone(config('app.timezone'));
            $attendance->save();
        } else {
            $attendance = Attendance::query()->create(
                [
                    'contract_id' => $contractId,
                    'date' => now()->setTimezone(config('app.timezone'))->format('Y-m-d'),
                    'start' => now()->setTimezone(config('app.timezone')),
                ]
            );
        }

        return $attendance;
    }

    public function deleteAttendance(int $id, int $contractId)
    {
        return Attendance::query()
            ->where('contract_id', $contractId)
            ->where('id', $id)
            ->delete();
    }

    public function getAttendancesByDay(string $day, string|array|null $contractIds)
    {
        if ($contractIds === null) {
            return collect();
        }

        $singleContract = is_string($contractIds);
        $currentDate = Carbon::parse($day);
        $contractFilterIds = $singleContract ? [$contractIds] : $contractIds;
        return User::query()
            ->where('id', Auth::user()->id)
            ->with(
                [
                    'contracts' => function ($q) use ($contractFilterIds) {
                        $q->whereIn('id', $contractFilterIds);
                    },
                    'contracts.attendances' => function ($q) use ($currentDate) {
                        $q->whereMonth('date', $currentDate->format('m'))
                            ->whereYear('date', $currentDate->format('Y'));
                    },
                    'contracts.attendancesValidations' => function ($q) use ($currentDate) {
                        $q->whereMonth('date', $currentDate->format('m'))
                            ->whereYear('date', $currentDate->format('Y'));
                    },
                    'contracts.planning'
                ])
            ->get();
    }

    public function buildSingleDayByContract($date, $contractId)
    {
        $dayData = Attendance::query()
            ->where('date', $date)
            ->where('contract_id', $contractId)
            ->get();

        return [
            'number' => $date->format('j'),
            'date' => $date,
            'day_name' => str($date->format('l'))->lower(),
            'month_name' => str($date->format('M'))->lower() . '.',
            'attendances' => $dayData,
            'total_seconds' => $dayData->sum('seconds'),
            'total_time' => $this->secondsToHm(
                $dayData->sum('seconds')
            )
        ];

    }
    public function buildSingleContractAttendances($currentDate, $data)
    {
        if ($currentDate === null) {
            $currentDate = now();
        }
        $calendar = \App\Models\Calendar::query()->whereYear('date', $currentDate)->get();

        $buildedAttendances = [];
        $days = range(1, $currentDate->daysInMonth);
        $contractInfo = [];
        try {
            foreach ($data as $user) {
                $contract = $user->contracts->first();
                if ($contract === null) {
                    continue;
                }
                $validations = $contract->attendancesValidations->first();
                $contractInfo[data_get($contract, 'id')] = [
                    'user_id' => data_get($user, 'id'),
                    'name' => data_get($user, 'name'),
                    'team' => data_get($contract, 'team.name'),
                    'contract_id' => data_get($contract, 'id'),
                    'business_id' => data_get($contract, 'business_id'),
                    'validations' => (!empty($validations) ? $validations->toArray() : null),
                ];

                $attendances = data_get($contract, 'attendances');

                $planningWorkDays = $this->getPeriods(
                    data_get($contract, 'planning.attributes.periods'),
                    $currentDate
                );

                $estimatedWorkTime = $this->getEstimatedWorkTime($planningWorkDays);

                foreach ($days as $day) {
                    $dateAttendance = Carbon::createFromDate($currentDate->format('Y-m-') . $day);
                    $dayData = $attendances->where('date', $dateAttendance);
                    $estimated = data_get($estimatedWorkTime, $dateAttendance->format('N'));
                    $differenceSeconds = data_get($estimated, 'seconds')-$dayData->sum('seconds');
                    $extraSeconds = data_get($estimated, 'seconds')-$dayData->sum('seconds');

                    $estimatedTimes = data_get($estimated, 'times');

                    $totalTimes = is_array($estimatedTimes) ? count($estimatedTimes) : 0;
                    $timesValids = 0;
                    foreach ($dayData as $row) {
                        $start = Carbon::parse(data_get($row, 'start'))->format('H:i:s');
                        $end = Carbon::parse(data_get($row, 'end'))->format('H:i:s');
                        foreach ($estimatedTimes as $time) {
                            if (in_array($start, [
                                data_get($time, 'from'),
                                data_get($time, 'to'),
                                ]) || in_array($end, [
                                    data_get($time, 'from'),
                                    data_get($time, 'to'),
                                ])) {
                                $timesValids++;
                            }
                        }
                    }

                    $timesValidated = $totalTimes === $timesValids;
                    $errors = data_get($estimated, 'seconds') > $dayData->sum('seconds') || $timesValidated === false;
                    $calendarDay = $calendar->where('date', $dateAttendance);

                    $workable = true;
                    if ($calendarDay->where('workable', false)->count() > 0) {
                        $workable = false;
                    }

                    $buildedAttendances[data_get($contract, 'id')][$day] = [
                        'number' => $day,
                        'calendar' => $calendarDay,
                        'date' => $dateAttendance,
                        'day_name' => str(\Carbon\Carbon::create($dateAttendance->format('Y'), $dateAttendance->format('m'), $day)->format('l'))->lower(),
                        'month_name' => str($dateAttendance->format('M'))->lower() . '.',
                        'attendances' => $dayData,
                        'total_seconds' => $dayData->sum('seconds'),
                        'total_time' => $this->secondsToHm(
                            $dayData->sum('seconds')
                        ),
                        'total_seconds_estimated' => $workable ? data_get($estimated, 'seconds') : 0,
                        'total_time_estimated' => $workable ? $this->secondsToHm(
                            data_get($estimated, 'seconds')
                        ) : $this->secondsToHm(),
                        'total_seconds_extra' => ($extraSeconds < 0 &&  data_get($estimated, 'seconds') > 0 ? -$extraSeconds : 0),
                        'total_time_extra' => ((($extraSeconds < 0 && data_get($estimated, 'seconds') > 0) || $workable == false) ? $this->secondsToHm(
                            $workable === false ? $dayData->sum('seconds') : -$extraSeconds
                        ) : null),
                        'errors' => $workable ? $errors : null,
                    ];
                }
            }

        } catch (\Exception $exception) {
            return [];
        }


        $buildedAttendancesSummary = $this->buildSummary($buildedAttendances, $contractInfo);
        return [$buildedAttendances, $buildedAttendancesSummary];
    }

    protected function buildSummary($buildedAttendances = [], $contractInfo = [])
    {
        $summary = [];
        foreach ($buildedAttendances as $contract => $days) {
            $daysCollected = collect($days);
            $totalSeconds = $daysCollected->sum('total_seconds');
            $totalSecondsEstimated = $daysCollected->sum('total_seconds_estimated');
            $difference = ($totalSeconds-$totalSecondsEstimated);

            $summary[$contract] = [
                'user' => $contractInfo[$contract] ?? [],
                'total_seconds' => $totalSeconds,
                'total_seconds_estimated' => $totalSecondsEstimated,
                'total_seconds_extra' => max($difference, 0),
                'total_time' => $this->secondsToHm($totalSeconds),
                'total_time_estimated' => $this->secondsToHm($totalSecondsEstimated),
                'total_time_extra' => $this->secondsToHm(max($difference, 0)),
            ];

            $summary[$contract]['status'] = $this->statusTime($summary[$contract]);
        }

        return $summary;
    }

    public function getTotalTimeByDay(string $day, array $contractIds): string
    {
        $attendances = Attendance::query()
            ->whereIn('contract_id', $contractIds)
            ->where('date', $day)
            ->get()
            ->map(function ($attendance, $key) {
                $attendance->startFormat = Carbon::create($attendance->start)->format('H:i');
                $attendance->endFormat = null;
                $attendance->seconds = Carbon::create(now())->diffInSeconds(Carbon::create($attendance->start));;
                if ($attendance->end) {
                    $attendance->endFormat = Carbon::create($attendance->end)->format('H:i');
                    $attendance->seconds = Carbon::create($attendance->end)->diffInSeconds(Carbon::create($attendance->start));
                }
                return $attendance;
            });

        return app(Attendances::class)->secondsToHm($attendances->sum('seconds'));
    }

    public function secondsToHm($seconds = 0)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $formattedDuration = sprintf('%dh %02dm', $hours, $minutes);
        return $formattedDuration;
    }

    public function calculateTiming($from, $to)
    {
        $from = Carbon::createFromFormat('H:i:s', $from);
        $to = Carbon::createFromFormat('H:i:s', $to);

        return $to->diffInSeconds($from);
    }

    public function statusTime($record)
    {
        $status = 3;

        if (data_get($record, 'total_seconds_estimated') == data_get($record, 'total_seconds')) {
            $status = 0;
        }

        if (data_get($record, 'total_seconds_extra') > 0) {
            $status = 1;
        }

        if (data_get($record, 'total_seconds_estimated') < data_get($record, 'total_seconds')) {
            $status = 2;
        }

        return $status;
    }

    public function getPeriods($periods, $currentDate) {
        $planningWorkDays = [];

        foreach ($periods as $period) {
            [$periodDateFrom, $periodDateUntil] = str(data_get($period, 'date'))->explode(' - ');
            [$periodDateFromDay, $periodDateFromMonth] = str($periodDateFrom)->explode('-');
            [$periodDateUntilDay, $periodDateUntilMonth] = str($periodDateUntil)->explode('-');

            $periodDateStart = Carbon::create($currentDate->format('Y'), $periodDateFromMonth, $periodDateFromDay);
            $periodDateEnd = Carbon::create($currentDate->format('Y'), $periodDateUntilMonth, $periodDateUntilDay);

            if ($currentDate->between($periodDateStart, $periodDateEnd)) {
                $planningWorkDays = data_get($period, 'work_days');
            }
        }

        return $planningWorkDays;
    }

    public function getEstimatedWorkTime($planningWorkDays)
    {
        return collect($planningWorkDays)->map(function ($day) {
            $times = collect(data_get($day, 'times'))->map(function ($time) {
                $time['seconds'] = $this->calculateTiming(data_get($time, 'from'), data_get($time, 'to'));
                return $time;
            });

            $day['times'] = $times->toArray();
            $day['seconds'] = $times->sum('seconds');
            return $day;
        })->mapWithKeys(function ($day) {;
            return [data_get($day, 'day') => $day];
        });
    }
}
