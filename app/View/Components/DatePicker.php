<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DatePicker extends Component
{
    public $startDate;
    public $endDate;

    public $endDateMin;

    public function render()
    {
        return view('livewire.date-picker');
    }


    public function updatedStartDate()
    {
        dd('hola');
        $this->validate([
            'startDate' => 'date|before_or_equal:endDate',
        ]);
    }

    public function updatedEndDate()
    {
        $this->validate([
            'endDate' => 'date|after_or_equal:startDate',
        ]);
    }
}
