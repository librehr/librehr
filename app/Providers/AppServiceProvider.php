<?php

namespace App\Providers;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\ServiceProvider;
use Filament\Notifications\Notification;
use Illuminate\View\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $this->attachViewToFilamentClasses(
            [
                \App\Filament\Pages\MyProfile\Profile::class,
                \App\Filament\Pages\MyProfile\ProfileContracts::class,
                \App\Filament\Pages\MyProfile\ProfileTools::class,
                \App\Filament\Pages\MyProfile\Documents::class,
            ],
            view: 'filament.pages.header.my-profile-navigation',
            hookName: 'panels::page.start'
        );


        $this->attachViewToFilamentClasses(
            [
                \App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\Requests::class,
                \App\Filament\Pages\TimeOff::class,
                \App\Filament\Pages\Attendances::class,
            ],
            view: 'filament.pages.header.user',
            hookName: 'panels::page.start'
        );


        $this->registerCalendar(
            \App\Filament\Pages\DeskBookings::class,
        );

        FilamentAsset::register([
            Js::make('alpinejs-tooltip', __DIR__ . '/../../resources/js/alpinejs-tooltip.js'),
            Css::make('leaflet-stylesheet', resource_path('css/leaflet.css')),
            Js::make('leaflet-script', resource_path('js/leaflet.js')),
        ]);
    }


    private function attachViewToFilamentClasses(
        array $classes,
        string $view,
        string $hookName,
        array $data = []
    ) {
        $links = [];
        foreach ($classes as $navigationClass) {
            $links[$navigationClass::getNavigationLabel()] = $navigationClass::getRouteName('app');
        }

        FilamentView::registerRenderHook($hookName,
            fn (): View => view($view, [
                'links' => $links,
                'data' => $data
            ]),
            scopes: $classes
        );
    }

    private function registerCalendar($myProfileNavigationClass)
    {
        $selectedMonth = request('m');
        $selectedYear = request('y');
        $selectedDay = request('d');

        $selected = Carbon::today();
        $next = null;
        if ($selectedYear !== null && $selectedMonth !== null && $selectedDay) {
            $selected = Carbon::create(
                $selectedYear,
                $selectedMonth,
                $selectedDay
            );
            $previous = Carbon::parse($selected)->subDay();
            $next = Carbon::parse($selected)->addDay();
        } else {
            $previous = Carbon::today()->subDay();
            $next = Carbon::parse($selected)->addDay();
        }

        $route = $myProfileNavigationClass::getRouteName('app');
        FilamentView::registerRenderHook(
            'panels::page.start',
            fn (): View => view('filament.pages.header.calendar', [
                'selected' => $selected->format('D d, F, Y'),
                'previous' =>  ($previous !== null && $previous < now()->startOfDay()  ? [
                    'y' => $previous->format('Y'),
                    'm' => $previous->format('m'),
                    'd' => $previous->format('d'),
                ] : null),
                'next' => ($next !== null && $next < now()->addDays(30)  ? [
                    'y' => $next->format('Y'),
                    'm' => $next->format('m'),
                    'd' => $next->format('d'),
                ] : null),
                'route' => $route
            ]),
            scopes: $myProfileNavigationClass
        );
    }
}
