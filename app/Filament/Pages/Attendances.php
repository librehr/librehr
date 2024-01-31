<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AttendancesChart;
use App\Livewire\AttendanceTodaySummary;
use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\HasParentActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\DataCollector\AjaxDataCollector;

class Attendances extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.attendances';
    protected static ?int $navigationSort = 1;

    public $contractId;
    public $year;
    public $month;
    public $selected;
    public $currentAttendance = null;
    public $days;
    public $summary;
    public $attendanceToBeDeleted = null;

    public $startValue;
    public $endValue;


    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->getActiveBusinessId() && $user->getActiveContractId();
    }

    public function mount()
    {
        $this->contractId = Auth::user()->getActiveContractId();;
        $selected = $this->loadSelectedDate();
        $this->loadDays($selected);
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.attendances-header');
    }

    public function getYearMonth()
    {
        return [$this->year, $this->month];
    }


    protected function getActions(): array
    {
        return [
            Action::make('add_time_action')
                ->icon('heroicon-m-plus-circle')
                ->iconPosition(IconPosition::After)
                ->iconButton()
                ->label('Add time')
                ->outlined()
                ->color('primary')
                ->slideOver()
                ->requiresConfirmation()
                ->form([
                    TimePicker::make('start')
                        ->required()
                        ->autofocus()
                        ->step(false),
                    TimePicker::make('end')
                        ->required()
                        ->step(false)
                ])
            ->action(function (array $arguments, $data) {
                app(\App\Services\Attendances::class)
                    ->startResumeAttendanceNow(
                        $this->contractId,
                        type: 'new',
                        date: data_get($arguments, 'date'),
                        start: data_get($data, 'start'),
                        end: data_get($data, 'end'),
                    );

                $this->reloadAttendances($this->selected);

            })->after(function () {
                Notification::make('ok')
                    ->title('Added successfully.')
                    ->success()
                    ->send();
            }),

            Action::make('delete_time_action')
                ->icon('heroicon-m-minus-circle')
                ->iconPosition(IconPosition::After)
                ->iconButton()
                ->label('Delete time')
                ->outlined()
                ->color('primary')
                ->slideOver()
                ->requiresConfirmation()
                ->action(function (array $arguments) {
                    app(\App\Services\Attendances::class)->deleteAttendance(
                        data_get($arguments, 'id'),
                        Auth::user()->getActiveContractId()
                    );

                    $this->reloadAttendances($this->selected);
                })->after(function () {
                    Notification::make('ok')
                        ->title('Removed successfully.')
                        ->success()
                        ->send();
                })
        ];
    }

    public function previous()
    {
        $this->selected = Carbon::parse($this->selected)->startOfMonth()->subMonth();
        $this->loadDays($this->selected);
    }

    public function next()
    {
        $this->selected = Carbon::parse($this->selected)->startOfMonth()->addMonth();
        $this->loadDays($this->selected);
    }

    public function createTime(): Action
    {
        return Action::make('edit')
            ->requiresConfirmation();
    }

    public function registerAttendanceNow(): void
    {
        $this->currentAttendance = app(\App\Services\Attendances::class)
            ->startResumeAttendanceNow($this->contractId);

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
        // prevent going to next month on over clicks
        if ($selected > now()) {
            $this->selected = now()->startOfMonth();
        }

        $this->dispatch('change-date-chart', selected: $this->selected)
            ->to(AttendancesChart::class);
        $this->days = [];
        $this->reloadAttendances($this->selected);
    }

    public function updateAttendance($id, $type, $value)
    {
        app(\App\Services\Attendances::class)->startResumeAttendanceNow(
            Auth::user()->getActiveContractId(),
            $id,
            type: $type,
            value: $value
        );

        $this->reloadAttendances($this->selected);
    }

    public function reloadAttendances($selected)
    {
        [$this->days, $this->summary] = app(\App\Services\Attendances::class)
            ->buildSingleContractAttendances(
                $selected,
                app(\App\Services\Attendances::class)
                ->getAttendancesByDay($selected, $this->contractId)
            );

        $this->currentAttendance = app(\App\Services\Attendances::class)
            ->getCurrentAttendance($this->contractId);

        $this->dispatch('update-summary')->to(AttendanceTodaySummary::class);
    }
}
