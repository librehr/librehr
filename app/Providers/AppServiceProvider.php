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
                \App\Filament\Pages\DeskBookings::class,
                \App\Filament\Pages\Attendances::class,
            ],
            view: 'filament.pages.header.user',
            hookName: 'panels::page.start'
        );

/*
        $this->registerCalendar(
            \App\Filament\Pages\AttendancesControl::class,
            true
        );
*/

        FilamentAsset::register([
            Js::make('alpinejs-tooltip', __DIR__ . '/../../resources/js/alpinejs-tooltip.js'),
            Css::make('leaflet-stylesheet', resource_path('css/leaflet.css')),
            Js::make('leaflet-script', resource_path('js/leaflet.js')),
            Css::make('tribute-css', 'https://unpkg.com/tributejs@5.1.3/dist/tribute.css'),
            Js::make('tribute-js', 'https://unpkg.com/tributejs@5.1.3/dist/tribute.min.js')
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

    private function registerCalendar($myProfileNavigationClass, $month = false)
    {
        $date = request('date');

        $selected = Carbon::today();
        $next = null;
        if ($date !== null) {
            $selected = Carbon::parse(
                $date
            );
            if ($month) {
                $previous = Carbon::parse($selected)->subMonth()->startOfMonth();
                $next = Carbon::parse($selected)->addMonth()->startOfMonth();
            } else {
                $previous = Carbon::parse($selected)->subDay();
                $next = Carbon::parse($selected)->addDay();
            }
        } else {
            if ($month) {
                $previous = Carbon::parse($selected)->subMonth()->startOfMonth();
                $next = Carbon::parse($selected)->addMonth()->startOfMonth();
            } else {
                $previous = Carbon::today()->subDay();
                $next = Carbon::parse($selected)->addDay();
            }
        }

        $route = $myProfileNavigationClass::getRouteName('app');
        FilamentView::registerRenderHook(
            'panels::page.start',
            fn (): View => view('filament.pages.header.calendar', [
                'month' => $month,
                'selected' => $month ? $selected->format('F, Y') : $selected->format('D d, F, Y'),
                'previous' =>  ($previous !== null && $previous < now()->startOfMonth()->startOfDay()  ? [
                    'date' => $previous->format('Y-m-d'),
                ] : null),
                'next' => ($next !== null && $next < now()  ? [
                    'date' => $next->format('Y-m-d'),
                ] : null),
                'route' => $route
            ]),
            scopes: $myProfileNavigationClass
        );
    }
}
