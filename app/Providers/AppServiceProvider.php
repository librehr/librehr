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

        /**
        $this->registerCalendar(
            \App\Filament\Pages\Attendances::class,
        );**/

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

        $selected = Carbon::today();
        $next = null;
        if ($selectedYear !== null && $selectedMonth !== null) {
            $selected = Carbon::create(
                $selectedYear,
                $selectedMonth
            );
            $previous = Carbon::parse($selected)->subMonth();
            $next = Carbon::parse($selected)->addMonth();
        } else {
            $previous = Carbon::today()->subMonth();
        }

        $route = $myProfileNavigationClass::getRouteName('app');
        FilamentView::registerRenderHook(
            'panels::page.start',
            fn (): View => view('filament.pages.header.calendar', [
                'selected' => $selected->format('F, Y'),
                'previous' => [
                    'y' => $previous->format('Y'),
                    'm' => $previous->format('m'),
                ],
                'next' => ($next !== null && $next < now()  ? [
                    'y' => $next->format('Y'),
                    'm' => $next->format('m'),
                ] : null),
                'route' => $route
            ]),
            scopes: $myProfileNavigationClass
        );
    }
}
