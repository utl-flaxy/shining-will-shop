<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Login;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Vite;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')

            ->login(Login::class)

            ->authGuard('web')

            ->middleware([
                'web',
            ])

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources',
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages',
            )

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets',
            )

            ->widgets([
                AccountWidget::class,
            ]);

            //->assets([
               // Css::make(
                    //'filament-colorme',
                    //Vite::asset('resources/css/filament-colorme.css')
                //),
            //]);
    }
}
