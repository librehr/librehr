<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\View\View;
use JetBrains\PhpStorm\NoReturn;

class Attendances extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.attendances';
    protected static ?int $navigationSort = 2;

    public $year;
    public $month;
    public $selected;

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.attendances-header');
    }

    #[NoReturn] public function mount()
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
    }

    public function getSubheading(): ?string
    {
        return __('Custom Page Subheading');
    }
}
