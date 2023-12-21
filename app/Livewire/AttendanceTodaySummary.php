<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Services\Attendances;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AttendanceTodaySummary extends Component
{
    public $totalTime;
    public $selected;
    public $currentAttendance;
    public function mount()
    {
        $this->reloadTodaySummary();
    }
    public function render()
    {
        return view('livewire.attendance-today-summary');
    }

    public function reloadTodaySummary()
    {
        $this->totalTime = app(Attendances::class)
            ->getTotalTimeByDay(data_get($this->currentAttendance, 'date', now()->format('Y-m-d')), [Auth::user()->getActiveContractId()]);
    }
}
