<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile\Profile;
use App\Models\Business;
use Carbon\Carbon;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Auth\EditProfile;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
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
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AppPanelProvider extends PanelProvider
{
    public function default()
    {
        return true;
    }
    public function panel(Panel $panel): Panel
    {
        FilamentView::registerRenderHook(
            'panels::user-menu.after',
            fn (): string => Blade::render('@livewire(\'navbar\')'),
        );

        $panel
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/app/theme.css')
            ->id('app')
            ->path('/')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
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
                'Business',
                'Administration'
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa()
            ->passwordReset()
            ->favicon(asset('images/logo.png'))
            ->default();


        return $panel;
    }
}
