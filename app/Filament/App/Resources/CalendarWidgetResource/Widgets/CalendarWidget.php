<?php

namespace App\Filament\App\Resources\CalendarWidgetResource\Widgets;

use App\Services\Calendar;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends Widget
{
    protected static string $view = 'filament.resources.calendar-widget';

    public $calendar = [];
    public $summary = [];

    public function mount($contractId, $absence){
        $absences = collect([$absence])->all();
        [$this->calendar, $this->summary]
            = app(Calendar::class)->buildCalendar($contractId,$year ?? date('Y'), $absences);
    }
}
