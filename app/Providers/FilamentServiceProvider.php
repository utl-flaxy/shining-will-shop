<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Filament::serving(function () {
            // public/css/filament-custom.css が存在すれば登録
            if (file_exists(public_path('css/filament-custom.css'))) {
                Filament::registerStyles([
                    asset('css/filament-custom.css'),
                ]);
            }
        });
    }
}
