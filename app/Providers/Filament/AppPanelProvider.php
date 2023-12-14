<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Carbon\Carbon;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        FilamentView::registerRenderHook(
            'panels::user-menu.before',
            fn (): string => Blade::render('@livewire(\'navbar\')'),
        );

        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => Blade::render('@livewire(\'header-notifications\')'),
        );

        $this->registerMyProfileNavigationClasses(
            [
                \App\Filament\Pages\MyProfile\Profile::class,
                \App\Filament\Pages\MyProfile\ProfileContracts::class,
                \App\Filament\Pages\MyProfile\Documents::class,
            ]
        );

        $this->registerCalendar(
            \App\Filament\Pages\Attendances::class,
        );

        return $panel
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/app/theme.css')
            ->id('app')
            ->path('app')
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->login()
            ->darkMode(false)
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->navigationGroups([
                'Inbox',
                'My Profile',
                'Human Resources',
                'Administration'
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa();
    }

    private function registerMyProfileNavigationClasses($myProfileNavigationClasses)
    {
        $links = [];
        foreach ($myProfileNavigationClasses as $navigationClass) {
            $links[$navigationClass::getNavigationLabel()] = $navigationClass::getRouteName('app');
        }

        FilamentView::registerRenderHook(
            'panels::page.start',
            fn (): View => view('filament.pages.header.my-profile-navigation', [
                'links' => $links
            ]),
            scopes: $myProfileNavigationClasses
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
