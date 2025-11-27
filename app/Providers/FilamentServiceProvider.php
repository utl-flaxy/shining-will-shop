<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages;
use Filament\Widgets;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\SaleResource;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Login::class)
            ->brandName('Shining-Will 管理画面')
            ->colors(['primary' => '#2563eb'])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([Pages\Dashboard::class])
            ->resources([
                ProductResource::class,
                CategoryResource::class,
                SaleResource::class,
            ])
            // ->widgets([
            //     Widgets\AccountWidget::class,
            // ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(false);
    }

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        // ✅ boot フェーズで明示的に登録
        Filament::registerPanel(
            $this->panel(app(Panel::class))
        );
    }
}
