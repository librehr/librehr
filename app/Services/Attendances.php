<?php

namespace App\Services;


use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Attendances extends BaseService
{
    public function getCurrentAttendance($contractId)
    {
        // Check if there is something openm just close it
        return Attendance::query()
            ->where('contract_id', $contractId)
            ->whereNull('end')
            ->first();
    }

    public function startResumeAttendanceNow($contractId)
    {
        $attendance = $this->getCurrentAttendance($contractId);
        if ($attendance) {
            $attendance->end = now()->setTimezone(config('app.timezone'));
            $attendance->save();
            $attendance = null;
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

    public function getAttendancesByDay(string $day, array $contractIds): array
    {
        $currentDate = Carbon::parse($day);
        $days = range(1, $currentDate->daysInMonth);
        $attendances = Attendance::query()
            ->whereIn('contract_id', $contractIds)
            ->whereMonth('date', $currentDate->format('m'))
            ->whereYear('date', $currentDate->format('Y'))
            ->get()
            ->mapToGroups(function ($attendance, $key) {
                $attendance->startFormat = Carbon::create($attendance->start)->format('H:i');
                $attendance->endFormat = null;
                $attendance->seconds = Carbon::create(now())->diffInSeconds(Carbon::create($attendance->start));;
                if ($attendance->end) {
                    $attendance->endFormat = Carbon::create($attendance->end)->format('H:i');
                    $attendance->seconds = Carbon::create($attendance->end)->diffInSeconds(Carbon::create($attendance->start));
                }
                return ["{$attendance->date->format('Y-m-j')}" => $attendance->toArray()];
            })->toArray();

        $month = [];
        foreach ($days as $day) {
            $attendancesFormat = data_get($attendances, $currentDate->format('Y-m-') . $day, []);
            if ($attendancesFormat === null) {
                continue;
            }
            $seconds = [];


            foreach ($attendancesFormat as $attendance) {
                $seconds[] = $attendance['seconds'];
            }

            //todo: meter contractid, esto no es funcional si paso varios contratos.
            $month[$day] = [
                'number' => $day,
                'date' => \Carbon\Carbon::create($currentDate->format('Y'), $currentDate->format('m'), $day)->format('Y-m-d'),
                'day_name' => str(\Carbon\Carbon::create($currentDate->format('Y'), $currentDate->format('m'), $day)->format('l'))->lower(),
                'month_name' => str($currentDate->format('M'))->lower() . '.',
                'attendances' => $attendancesFormat,
                'total_seconds' => $this->secondsToHm(array_sum($seconds))
            ];
        }

        return $month;
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
