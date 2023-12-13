<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
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


        $this->registerMyProfileNavigationClasses(
            [
                \App\Filament\Pages\MyProfile\Profile::class,
                \App\Filament\Pages\MyProfile\ProfileContracts::class,
                \App\Filament\Pages\MyProfile\Documents::class,
            ]
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
            fn (): View => view('filament.pages.my-profile.navigation', [
                'links' => $links
            ]),
            scopes: $myProfileNavigationClasses
        );
    }
}
