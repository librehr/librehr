<?php

namespace App\Filament\App\Pages;

use App\Filament\Pages\view;
use App\Models\Calendar;
use App\Models\DeskBooking;
use App\Models\Post;
use App\Models\Requestable;
use App\Models\Room;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Dashboard extends Page
{
    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    /**
     * @var view-string
     */
    protected static string $view = 'filament.pages.dashboard';

    public $contractId;
    public $businessId;
    public $todayBooked = false;

    public $currentAttendance;
    public $posts = [];

    public $notifications;
    public $requests;
    public $notificationsCount = 0;
    public $requestsCount = 0;
    public $user;
    public $calendar;
    public $tasks;


    public function mount()
    {
        $this->user = \Auth::user();
        $this->contractId = $this->user->getActiveContractId();
        $this->businessId = $this->user->getActiveBusinessId();
        $this->posts = Post::query()
            ->where('business_id', $this->businessId)
            ->latest()
            ->limit(20)
            ->get();

        $this->currentAttendance = app(\App\Services\Attendances::class)
            ->getCurrentAttendance($this->contractId);

        $this->getNotifications();
        $this->getRequests();
        $this->getCalendar();
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('filament-panels::pages/dashboard.title');
    }

    public static function getNavigationIcon(): ?string
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? 'heroicon-m-home' : 'heroicon-o-home');
    }

    public static function routes(Panel $panel): void
    {
        Route::get(static::getRoutePath(), static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name(static::getSlug());
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('filament-panels::pages/dashboard.title');
    }

    public function registerAttendanceNow(): void
    {
        $this->currentAttendance = app(\App\Services\Attendances::class)
            ->startResumeAttendanceNow($this->contractId);
    }

    public function getNotifications()
    {
        $this->notificationsCount = Auth::user()->notifications->whereNull('read_at')->count();
    }

    public function getRequests()
    {
        $this->requestsCount = Requestable::query()->where('user_id', data_get(Auth::user(), 'id'))->count();
    }

    public function goToRequests()
    {
        $this->redirectRoute('filament.app.pages.requests');
    }

    public function getCalendar()
    {
        $this->calendar = Calendar::query()
            ->whereYear('date', now())
            ->get();
    }
}
