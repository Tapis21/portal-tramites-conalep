<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard as AppDashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->colors([
                'primary' => Color::hex('#006937'),
                'gray' => Color::Slate,
            ])
            ->brandName('CONALEP II')
            ->brandLogo(asset('images/Quintana-Roo_3.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/286-Cancún-II.ico'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AppDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\EstadisticasGenerales::class,
                \App\Filament\Widgets\SolicitudesPendientesSS::class,
                \App\Filament\Widgets\SolicitudesPendientesPP::class,
                \App\Filament\Widgets\ProximasFinalizacionesSS::class,
                \App\Filament\Widgets\ProximasFinalizacionesPP::class,
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
                \App\Http\Middleware\AdminMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // ================================================================
            // 📋 NAVIGATION GROUPS (SECCIONES DEL MENÚ)
            // ================================================================
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('📋 General')
                    ->collapsible(true)
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label('📁 Gestión de Trámites')
                    ->collapsible(true)
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label('⚙️ Configuración')
                    ->collapsible(true)
                    ->collapsed(false),
            ])
            // ================================================================
            // 🚀 SOLO EL DASHBOARD
            // ================================================================
            ->navigationItems([
                NavigationItem::make('dashboard')
                    ->label('Escritorio')
                    ->icon('heroicon-o-home')
                    ->url('/admin/dashboard')
                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard'))
                    ->group('📋 General'),
            ]);
    }
}