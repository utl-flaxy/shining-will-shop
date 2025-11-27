<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages;
use Filament\Widgets;
use App\Filament\Pages\Login;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->authGuard('filament') // ← ✅ Filament専用ガードを使用
            ->middleware(['web'])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
            ]);
    }
}
