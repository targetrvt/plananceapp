<?php

namespace App\Providers\Filament;

use App\Filament\GlobalSearch\AppGlobalSearchProvider;
use App\Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use App\Filament\Pages\Auth\Register;
use App\Http\Middleware\RedirectPremiumUsersFromFreePanel;
use App\Livewire\BrowserSessions;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Livewire\Livewire;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

class AppPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        Livewire::component('browser_sessions', BrowserSessions::class);
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->passwordReset()
            ->registration(Register::class)
            ->emailVerification(EmailVerificationPrompt::class)
            ->databaseNotifications()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'primary' => Color::Teal,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Poppins')
            ->brandLogo(asset('images/Planancelogo.png'))
            ->brandLogoHeight('4rem')
            ->navigationGroups([
                __('filament.navigation.groups.Overview'),
                __('filament.navigation.groups.Management'),
                __('filament.navigation.groups.Pricing'),
            ])
            ->favicon(url('images/Planancelogomini.png'))
            ->globalSearch(AppGlobalSearchProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->authMiddleware([
                Authenticate::class,
                RedirectPremiumUsersFromFreePanel::class,
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(
                        MyImages::make()
                            ->directory('images/background')
                    ),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        hasAvatars: true
                    )
                    ->enableTwoFactorAuthentication(true, false)
                    ->myProfileComponents([
                        'browser_sessions' => BrowserSessions::class,
                    ]),
            ]);
    }
}
