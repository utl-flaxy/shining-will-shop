<?php

use Illuminate\Support\Facades\Route;
use Filament\Pages\Auth\Login;

Route::prefix('admin')
    ->name('filament.admin.')
    ->group(function () {
        // GET /admin/login （既存と同じ）
        Route::get('/login', [Login::class, 'mount'])->name('auth.login');

        // ✅ POST /admin/login を明示的に登録
        Route::post('/login', [Login::class, 'authenticate'])->name('auth.login.authenticate');
    });
