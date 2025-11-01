<?php

namespace App\Providers;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

class AdminShiningPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default() // ★ これがないと「No default Filament panel is set.」
            ->id('adminShining')
            ->path('adminShining') // /adminShining でアクセス
            ->login()
            ->discoverResources(
                in: app_path('Filament/AdminShining/Resources'),
                for: 'App\\Filament\\AdminShining\\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/AdminShining/Pages'),
                for: 'App\\Filament\\AdminShining\\Pages',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
