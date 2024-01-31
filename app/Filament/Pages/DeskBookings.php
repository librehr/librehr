<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PlaceResource\Pages\Floors;
use App\Models\Absence;
use App\Models\DeskBooking;
use App\Models\Floor;
use App\Models\Place;
use App\Models\Request;
use App\Models\Requestable;
use App\Models\Room;
use App\Models\User;
use App\Notifications\DeskBooked;
use App\Services\Calendar;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use function PHPUnit\Framework\isJson;

class DeskBookings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected $listeners = ['floorsUpdated' => 'floorsUpdated'];

    protected static string $view = 'filament.pages.desk-bookings';

    public $date;
    public $selected;
    public $record = [];
    public $places = [];
    public $place = null;
    public $floors = [];
    public $floor = null;
    public $rooms = [];
    public $room = null;
    public $todayBooked = 0;
    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->getActiveBusinessId() && $user->getActiveContractId();
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.header.desk-bookings');
    }

    public function updatedFloor($value)
    {
        $this->record = [];
        $this->rooms = Room::query()->with('desks')->where('floor_id', $value)->get();
    }

    #[On('free-seat')]
    public function freeSeat($seat)
    {
        $data = json_decode(base64_decode($seat), true);
        $bookingId = data_get($data, 'bookings.0.id');

        $booking = DeskBooking::query()
            ->where('id', $bookingId)
            ->where('contract_id', Auth::user()->getActiveContractId())
            ->with('desk')
            ->first();
        $originalBooking = $booking;
        $booking->delete();
        $this->redirectRoute('filament.app.pages.desk-bookings', [
            'date' => $this->date,
            'room' => data_get($originalBooking, 'desk.room_id')
        ], navigate: false);
    }
    #[On('book-seat')]
    public function bookSeat($seat)
    {
        $data = json_decode(base64_decode($seat), true);

        $booking = DeskBooking::query()->create([
            'desk_id' => data_get($data, 'id'),
            'contract_id' => Auth::user()->getActiveContractId(),
            'business_id' => Auth::user()->getActiveBusinessId(),
            'start' => Carbon::parse($this->date)->startOfDay(),
            'end' => Carbon::parse($this->date)->endOfDay(),
        ]);

        // fee other desks
        if ($booking) {
            $deleted = DeskBooking::query()
                ->where('id', '!=', data_get($booking, 'id'))
                ->where('contract_id', Auth::user()->getActiveContractId())
                ->whereDate('start', $this->date)
                ->delete();
        }

        Auth::user()->notify(
            Notification::make()
                ->title('Seat #' . data_get($data, 'name') . ' reserved for ' . $this->selected)
                ->toDatabase()
        );

        $this->redirectRoute('filament.app.pages.desk-bookings', [
            'date' => $this->date,
            'room' => data_get($booking, 'desk.room_id')
        ], navigate: false);
    }
    public function updatedRoom($value = null)
    {
        $this->getSelectedDate();
        if ($value === null) {
            $value = data_get($this->record, 'id');
        }

        $this->getBookings($value);

        $this->dispatch('render-map', ['record' => $this->record]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = app(self::class)->getBookedToday()->count();
        return $count > 0  ? $count : null;
    }

    public function mount()
    {
        $contract = Auth::user()->getActiveContract();
        $this->todayBooked = app(self::class)->getBookedToday()->first();
        $this->places = $contract->place;
        $this->floors = $this->places->floors;

        if (request()->get('date')) {
            $this->date = Carbon::parse(\request()->get('date'))->format('Y-m-d');
        } else {
            $this->date = now()->format('Y-m-d');
        }

        $this->getSelectedRoom();
    }

    public function updatedDate()
    {

    }

    protected function getSelectedDate()
    {
        $request =  \request()->all();
        $this->selected = now();
        if (
            in_array('y', array_keys($request))
            && in_array('d', array_keys($request))
            && in_array('m', array_keys($request))
        ) {
            $this->selected = Carbon::create($request['y'],$request['m'], $request['d']);
        }
    }

    protected function getSelectedRoom()
    {
        $roomId = \request()->get('room');

        $room = Room::query()->with(['floor:id,place_id'])->find($roomId);

        if (!$room) {
            return;
        }

        $placeId = data_get($room, 'floor.place_id');
        $floorId = data_get($room, 'floor.id');
        $place = Place::query()->with(['floors', 'floors.rooms'])->find($placeId);

        $this->place = $place;
        $this->floors = data_get($place, 'floors');
        $floor = collect($this->floors)->where('id', $floorId)->first();
        $this->floor = data_get($floor, 'id');

        $this->rooms = collect(data_get($floor, 'rooms', []))->where('floor_id', $floorId)->all();

        $this->getBookings(data_get($room, 'id'));
    }

    public function getBookings($value)
    {
        $this->record = Room::query()->with(['desks', 'desks.bookings' => function ($query) {
            $query->whereDate('start', $this->date)
                ->with('contract.user:id,name');
        }])->find($value);

        $this->room = $value;
    }

    public function resetAll()
    {
        $this->redirectRoute('filament.app.pages.desk-bookings', [
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);
    }

    public function getBookedToday()
    {
        return DeskBooking::query()
            ->whereDate('start', now())
            ->where('contract_id', Auth::user()->getActiveContractId());
    }

    public function goToDeskBookings($room = null)
    {
        $this->redirectRoute('filament.app.pages.desk-bookings', $room !== null ? ['room' => $room] : null);
    }
}
