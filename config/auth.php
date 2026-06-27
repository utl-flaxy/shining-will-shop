<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [

        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // ✅ オーナー専用ガード（これが無かった）
        'owner' => [
            'driver' => 'session',
            'provider' => 'owners',
        ],
    ],

    'providers' => [

        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // ✅ オーナー用プロバイダ
        'owners' => [
            'driver' => 'eloquent',
            'model' => App\Models\Owner::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
        ],
    ],

];
