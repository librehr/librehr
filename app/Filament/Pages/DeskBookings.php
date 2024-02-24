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
    protected static ?int $navigationSort = 3;
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

    public function updatedPlace($value = null)
    {
        $this->floors = Floor::query()->where('place_id' , $value)->get();
        $this->rooms = [];
        $this->floor = null;
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
        $args = [];
        if ($this->date){
            $args['date'] = $this->date;
        }
        $args['room'] = $value;
        $this->redirectRoute('filament.app.pages.desk-bookings', $args, false, false);
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
        $this->places = Place::query()->with('floors')->get();
        $this->place = data_get($contract, 'place.id');
        $this->updatedPlace($this->place);
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

        $room = Room::query()->with(['floor.place.floors'])->find($roomId);

        if (!$room) {
            return;
        }

        $placeId = data_get($room, 'floor.place.id');
        $floorId = data_get($room, 'floor.id');
        $floors = data_get($room, 'floor.place.floors');

        $this->place = $placeId;
        $this->floors = $floors;
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

    public function goToDeskBookings($room = null, $date = null)
    {
        $args = [];
        if ($date){
            $args['date'] = Carbon::parse($date)->format('Y-m-d');
        }

        if ($room) {
            $args['room'] = $room;
        }

        $this->redirectRoute('filament.app.pages.desk-bookings', $args);
    }
}
