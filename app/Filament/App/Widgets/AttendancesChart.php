<?php

namespace App\Filament\App\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class AttendancesChart extends ChartWidget
{
    protected static ?string $maxHeight = '80px';
    protected static string $color = 'primary';

    public $selected;
    public $days;
    public $summary;

    public function mount(): void
    {
        parent::mount(); // TODO: Change the autogenerated stub

        $this->selected = now();
    }

    #[On('change-date-chart')]
    public function changeDateChart($selected)
    {
        $this->selected = $selected;
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $contractId = \Auth::user()->getActiveContractId();
        $selected = $this->selected ?? now();
        if (is_string($selected)) {
            $selected = Carbon::parse($selected);
        }

        [$this->days, $this->summary] = app(\App\Services\Attendances::class)
            ->buildSingleContractAttendances(
                $selected,
                app(\App\Services\Attendances::class)
                    ->getAttendancesByDay($selected, $contractId)
            );

        $data = collect(data_get($this->days, $contractId))
            ->map(function ($day) {
                return round(data_get($day, 'total_seconds') / 60 / 60, 2);
            })->toArray();

        $totalDays = range(1, $selected->daysInMonth);
        return [
            'datasets' => [
                [
                    'label' => 'Worked hours',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
