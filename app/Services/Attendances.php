<?php

namespace App\Services;


use App\Models\Attendance;
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
            ->with(
                [
                    'contracts' => function ($q) use ($contractFilterIds) {
                        $q->whereIn('id', $contractFilterIds);
                    },
                    'contracts.attendances' => function ($q) use ($currentDate) {
                        $q->whereMonth('date', $currentDate->format('m'))
                            ->whereYear('date', $currentDate->format('Y'));
                    }
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
        $buildedAttendances = [];
        $days = range(1, $currentDate->daysInMonth);
        try {
            $attendances = data_get($data->first()->contracts->first(), 'attendances');

            foreach ($days as $day) {
                //todo: meter contractid, esto no es funcional si paso varios contratos.
                $dateAttendance = Carbon::createFromDate($currentDate->format('Y-m-') . $day);
                $dayData = $attendances->where('date', $dateAttendance);
                $buildedAttendances[$day] = [
                    'number' => $day,
                    'date' => $dateAttendance,
                    'day_name' => str(\Carbon\Carbon::create($dateAttendance->format('Y'), $dateAttendance->format('m'), $day)->format('l'))->lower(),
                    'month_name' => str($dateAttendance->format('M'))->lower() . '.',
                    'attendances' => $dayData,
                    'total_seconds' => $dayData->sum('seconds'),
                    'total_time' => $this->secondsToHm(
                        $dayData->sum('seconds')
                    ),
                ];
            }

            logger(collect($buildedAttendances)->count());
        } catch (\Exception $exception) {
            return [];
        }

        return $buildedAttendances;
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

    public function secondsToHm($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $formattedDuration = sprintf('%dh %02dm', $hours, $minutes);
        return $formattedDuration;
    }
}
