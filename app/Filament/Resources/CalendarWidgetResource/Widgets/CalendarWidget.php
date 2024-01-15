<?php

namespace App\Filament\Resources\CalendarWidgetResource\Widgets;

use App\Services\Calendar;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends Widget
{
    protected static string $view = 'filament.resources.calendar-widget';

    public $calendar;

    public function mount($absence){
        $absences = collect([$absence])->all();
        $this->calendar = app(Calendar::class)->buildCalendar($year ?? date('Y'), $absences);
    }
}
