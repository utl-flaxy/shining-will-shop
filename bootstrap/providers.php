<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    // ← この2行を必ず追加！
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\AdminShiningPanelProvider::class,
];
