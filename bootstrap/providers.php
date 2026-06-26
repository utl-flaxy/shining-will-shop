<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    // ✅ 残すのはこの1行のみ
    App\Providers\Filament\AdminShiningPanelProvider::class,
];
