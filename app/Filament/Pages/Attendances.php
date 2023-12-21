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
    }

    public function registerAttendanceNow(): void
    {
        $this->currentAttendance = app(\App\Services\Attendances::class)
            ->startResumeAttendanceNow(Auth::user()->getActiveContractId());
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
            app(\App\Services\Attendances::class)->deleteAttendance(
                $this->attendanceToBeDeleted,
                Auth::user()->getActiveContractId()
            );
        }
        $this->attendanceToBeDeleted = null;
        $this->dispatch('close-modal', id: 'confirm-delete-attendance');
        $this->reloadAttendances($this->selected);
    }

    public function reloadAttendances($selected)
    {
        //dd('e');
        $contractId = Auth::user()->getActiveContractId();
        $this->days = app(\App\Services\Attendances::class)
            ->getAttendancesByDay($selected, [$contractId]);
        $this->currentAttendance = app(\App\Services\Attendances::class)
            ->getCurrentAttendance(Auth::user()->getActiveContractId());
    }
}
