<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages;
use Filament\Widgets;
use App\Filament\Pages\Login;
use Filament\Support\Assets\Css;
use Illuminate\Support\Facades\Vite;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')

            // ✅ ログイン画面
            ->login(Login::class)

            // ✅ Filament専用ガード
            ->authGuard('filament')

            // ✅ ミドルウェア
            ->middleware(['web'])

            // ✅ ダッシュボード
            ->pages([
                Pages\Dashboard::class,
            ])

            // ✅ ウィジェット
            ->widgets([
                Widgets\AccountWidget::class,
            ])

            // ✅ Shining Will 専用テーマCSSを読み込む（最重要）
            ->assets([
                Css::make(
                    'filament-colorme',
                    Vite::asset('resources/css/filament-colorme.css')
                ),
            ]);
    }
}
