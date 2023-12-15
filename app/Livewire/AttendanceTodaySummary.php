<?php

namespace App\Livewire;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AttendanceTodaySummary extends Component
{
    public $totalSeconds;
    public $currentAttendance;
    public function mount()
    {
        $this->currentAttendance = Attendance::getCurrentAttendance();
        $this->reloadTodaySummary();
    }
    public function render()
    {

        return view('livewire.attendance-today-summary');
    }

    public function reloadTodaySummary()
    {
        [$user, $contract, $attendance] = $this->currentAttendance;
        $attendances = Attendance::query()
            ->where('contract_id', $contract)
            ->where('date', data_get($attendance, 'date', now()->format('Y-m-d')))
            ->get()
            ->map(function ($attendance, $key) {
                $attendance->startFormat = Carbon::create($attendance->start)->format('H:m');
                $attendance->endFormat = null;
                $attendance->seconds = Carbon::create(now())->diffInSeconds(Carbon::create($attendance->start));;
                if ($attendance->end) {
                    $attendance->endFormat = Carbon::create($attendance->end)->format('H:m');
                    $attendance->seconds = Carbon::create($attendance->end)->diffInSeconds(Carbon::create($attendance->start));
                }
                return $attendance->toArray();
            });

        $this->totalSeconds = $this->secondsToHm(array_sum($attendances->pluck('seconds')->toArray()));
    }

    private function secondsToHm($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $formattedDuration = sprintf('%dh %02dm', $hours, $minutes);
        return $formattedDuration;
    }
}
