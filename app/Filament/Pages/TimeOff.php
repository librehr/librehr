<?php

namespace App\Filament\Pages;

use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\Request;
use App\Services\Calendar;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use function PHPUnit\Framework\isJson;

class TimeOff extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static string $view = 'filament.pages.time-off';

    public $contractId;
    public $year = null;
    public $calendar = [];
    public $from = 'ysyyas';
    public $type;

    public $absenceTypeId;
    public $startDate = 0;
    public $endDate;
    public $endDateMin;
    public $comments;

    public $contractAbsences;

    public function getTitle(): string|Htmlable
    {
        return 'Time Off (' . $this->year . ')'; // TODO: Change the autogenerated stub
    }

    public function updatedStartDate()
    {
        if ($this->endDate !== null) {
            $this->validate([
                'startDate' => 'date|before_or_equal:endDate',
            ]);

        }

        $this->endDateMin = $this->startDate;
    }

    public function updatedEndDate()
    {
        $this->validate([
            'endDate' => 'date|after_or_equal:startDate',
        ]);

        $days = Carbon::createFromFormat('Y-m-d', $this->startDate)->diffInDays(
            Carbon::createFromFormat('Y-m-d', $this->endDate)
        );

        $overlaps = ['Anonym', 'Invite'];
        session()->flash('days', 'Total days ' . $days + 1);
        session()->flash('overlap', 'Overlaps with ' . implode(',', $overlaps) . ' in your team.');
    }

    public function submitRequestTimeOff()
    {
        $user = Auth::user();
        $absence = Absence::query()->create([
            'absence_type_id' => $this->absenceTypeId,
            'contract_id' => $this->contractId,
            'start' => $this->startDate,
            'year' => Carbon::parse($this->startDate)->format('Y'),
            'end' => $this->endDate,
            'status' => 'pending',
            'comments' => $this->comments,
        ]);

        $request = Request::query()->firstWhere('name', 'absences');
        $absence->requests()->attach($absence->id, [
            'request_id' => data_get($request, 'id'),
            'user_id' => 1,
            'contract_id' => $user->getActiveContractId(),
            'created_at' => now(),
        ]);

        Notification::make()
            ->title('Requested successfully')
            ->success()
            ->send();

        $this->startDate = null;
        $this->endDate = null;
        $this->comments = null;
        $this->dispatch('close-modal', id: 'request-absence');
        $this->reloadAbsences();
    }

    public function mount()
    {
        $this->contractId = Auth::user()->getActiveContractId();
        $this->year = date('Y');
        $this->loadCalendar($this->year);

        $this->type = AbsenceType::query()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->reloadAbsences();
    }

    public function updateYear($type)
    {
        $this->year = $type === 'next' ? $this->year + 1 : $this->year - 1;
        $this->loadCalendar($this->year);
        $this->reloadAbsences();

    }

    public static function getNavigationBadge(): ?string
    {
        /*
        Auth::user()->notify(
            new \App\Notifications\Requests(
                'Ya tienes disponible tu nómina!',
                'Ya puedes entrar y ver tu nómina.',
                'wtf.com',
                'Ver'
            )
        );
        */

        return Auth::user()->unreadNotifications->count(); // TODO: Change the autogenerated stub
    }

    public function openAbsence($id)
    {
        $this->dispatch('open-modal', id: 'view-absence');
    }
    public function loadCalendar($year = null)
    {
        $absences = Absence::query()
            ->whereIn('contract_id', [$this->contractId])
            ->whereYear('start', $year)
            ->where('status', 'allowed')
            ->orderBy('start')
            ->get();

        $this->calendar = app(Calendar::class)->buildCalendar($year ?? date('Y'), $absences);
    }

    public function reloadAbsences()
    {
        $this->contractAbsences = Absence::query()
            ->where('contract_id', $this->contractId)
            ->whereYear('start', $this->year)
            ->orderBy('start')
            ->get()
            ->groupBy('status')
            ->toArray();
    }
}
