<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\DataCollector\AjaxDataCollector;

class Attendances extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.attendances';
    protected static ?int $navigationSort = 2;

    public $year;
    public $month;
    public $selected;
    public $currentAttendance = null;
    public $days;
    public $attendanceToBeDeleted = null;

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.attendances-header');
    }

    public function mount()
    {
        $selected = $this->loadSelectedDate();
        $this->loadDays($selected);
        $this->getCurrentAttendance();
    }

    public function getCurrentAttendance()
    {
        $user = Auth::user();
        $activeContract = $user->getActiveContractId();
        // Check if there is something openm just close it
        $attendance = Attendance::query()
            ->where('contract_id', $activeContract)
            ->whereNull('end')
            ->first();

        if ($attendance) {
            $this->currentAttendance = $attendance;
        }

        return [
            $user, $activeContract, $attendance
        ];
    }
    public function registerAttendanceNow()
    {
        [$user, $activeContract, $attendance] = $this->getCurrentAttendance();
        if ($attendance) {
            $attendance->end = now()->setTimezone(config('app.timezone'));
            $attendance->save();
            $attendance = null;
        } else {
            $attendance = Attendance::query()->create(
                [
                    'contract_id' => $activeContract,
                    'date' => now()->setTimezone(config('app.timezone'))->format('Y-m-d'),
                    'start' => now()->setTimezone(config('app.timezone')),
                ]
            );
        }

        $this->currentAttendance = $attendance;
        $this->reloadAttendances($this->selected);
    }

    public function getSubheading(): ?string
    {
        return __('Custom Page Subheading');
    }

    private function loadSelectedDate()
    {
        $this->year = request()->get('y');
        $this->month = request()->get('m');

        $selectedMonth = request('m');
        $selectedYear = request('y');

        $selected = Carbon::today();
        if ($selectedYear !== null && $selectedMonth !== null) {
            $selected = Carbon::create(
                $selectedYear,
                $selectedMonth
            );
        }

        $this->selected = $selected;
        return $selected;
    }
    private function loadDays($selected)
    {
        $this->days = [];
        $this->reloadAttendances($selected);
    }

    public function deleteAttendance($id)
    {
        $this->attendanceToBeDeleted = $id;
        $this->dispatch('open-modal', id: 'confirm-delete-attendance');
    }

    public function confirmDeleteAttendance($delete)
    {
        if ($delete) {
            Attendance::query()
                ->where('contract_id', Auth::user()->getActiveContractId())
                ->where('id', $this->attendanceToBeDeleted)
                ->delete();
        }
        $this->attendanceToBeDeleted = null;
        $this->dispatch('close-modal', id: 'confirm-delete-attendance');
        $this->reloadAttendances($this->selected);
    }

    public function reloadAttendances($selected, $filterDay = null)
    {

        $days = range(1, $selected->daysInMonth);
        $attendances = Attendance::query();

        if ($filterDay !== null) {
            $attendances = $attendances->whereDay('date', $filterDay);
        } else {
            $this->days = [];
        }

        $attendances = $attendances
            ->where('contract_id', Auth::user()->getActiveContractId())
            ->whereMonth('date', $selected->format('m'))
            ->whereYear('date', $selected->format('Y'))
            ->get()
            ->mapToGroups(function ($attendance, $key) {

                $attendance->startFormat = Carbon::create($attendance->start)->format('H:m');
                $attendance->endFormat = null;
                $attendance->seconds = Carbon::create(now())->diffInSeconds(Carbon::create($attendance->start));;
                if ($attendance->end) {
                    $attendance->endFormat = Carbon::create($attendance->end)->format('H:m');
                    $attendance->seconds = Carbon::create($attendance->end)->diffInSeconds(Carbon::create($attendance->start));
                }
                return ["{$attendance->date->format('Y-m-j')}" => $attendance->toArray()];
            })->toArray();

        foreach ($days as $day) {
            $attendancesFormat = data_get($attendances, $selected->format('Y-m-') . $day, []);
            if ($attendancesFormat === null) {
                continue;
            }
            $seconds = [];

            foreach ($attendancesFormat as $attendance) {
                $seconds[] = $attendance['seconds'];
            }

            $this->days[$day] = [
                'number' => $day,
                'date' => \Carbon\Carbon::create($selected->format('Y'), $selected->format('m'), $day)->format('Y-m-d'),
                'day_name' => str(\Carbon\Carbon::create($selected->format('Y'), $selected->format('m'), $day)->format('l'))->lower(),
                'month_name' => str($selected->format('M'))->lower() . '.',
                'attendances' => $attendancesFormat,
                'total_seconds' => $this->secondsToHm(array_sum($seconds))
            ];
        }
    }

    public function reloadTodaySummary()
    {
        $this->reloadAttendances($this->selected, now()->format('j'));
    }

    private function secondsToHm($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $formattedDuration = sprintf('%dh %02dm', $hours, $minutes);
        return $formattedDuration;
    }
}
