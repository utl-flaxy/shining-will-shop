<?php

use App\Filament\Pages\Login;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\SaleResource;
use App\Filament\Resources\OrderResource; // ← ✅ 注文管理を追加
use Filament\Pages;
use Filament\Widgets;
use Filament\Facades\Filament;

return [

    /*
    |--------------------------------------------------------------------------
    | 🌸 デフォルトパネル
    |--------------------------------------------------------------------------
    */
    'default_panel' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | 🧭 パネル定義
    |--------------------------------------------------------------------------
    */
    'panels' => [

        'admin' => [
            'id' => 'admin',
            'path' => 'admin',
            'guard' => 'web',
            'auth_guard' => 'web',
            'middleware' => ['web'],

            /*
            |--------------------------------------------------------------------------
            | 🔐 認証ページ設定
            |--------------------------------------------------------------------------
            */
            'login' => Login::class,

            /*
            |--------------------------------------------------------------------------
            | 📦 管理対象リソース
            |--------------------------------------------------------------------------
            */
            'resources' => [
                ProductResource::class,
                CategoryResource::class,
                SaleResource::class,
                OrderResource::class, // ✅ ここを追加
            ],

            /*
            |--------------------------------------------------------------------------
            | 🔍 自動検出設定
            |--------------------------------------------------------------------------
            */
            'discover' => [
                'resources' => [
                    'in' => app_path('Filament/Resources'),
                    'for' => 'App\\Filament\\Resources',
                ],
                'pages' => [
                    'in' => app_path('Filament/Pages'),
                    'for' => 'App\\Filament\\Pages',
                ],
                'widgets' => [
                    'in' => app_path('Filament/Widgets'),
                    'for' => 'App\\Filament\\Widgets',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 🏠 管理画面ページ・ウィジェット
            |--------------------------------------------------------------------------
            */
            'pages' => [
                Pages\Dashboard::class,
            ],

            'widgets' => [
                // Widgets\AccountWidget::class, // ← 非表示（左メニューをシンプル化）
            ],

            /*
            |--------------------------------------------------------------------------
            | 🏷 ブランド・ロゴ
            |--------------------------------------------------------------------------
            */
            'brand' => 'Shining Will 管理画面',
            'brandLogo' => env('APP_URL') . '/images/logo-admin.svg',
            'favicon'   => env('APP_URL') . '/images/favicon.ico',

            /*
            |--------------------------------------------------------------------------
            | 🎨 テーマ設定
            |--------------------------------------------------------------------------
            */
            'viteTheme' => 'resources/css/filament/admin/theme.css',

            'theme' => [
                'path' => resource_path('css/filament/admin/theme.css'),
            ],

            /*
            |--------------------------------------------------------------------------
            | 🌙 外観設定
            |--------------------------------------------------------------------------
            */
            'dark_mode' => false,

            /*
            |--------------------------------------------------------------------------
            | 🌐 ローカライズ設定
            |--------------------------------------------------------------------------
            */
            'locale' => 'ja',
            'timezone' => 'Asia/Tokyo',

            /*
            |--------------------------------------------------------------------------
            | 🧩 UI設定（Filament::serving で追加カスタマイズ）
            |--------------------------------------------------------------------------
            */
            'serving' => function () {
                Filament::registerRenderHook(
                    'head.end',
                    fn () => <<<HTML
                    <style>
                        /* ===== 🌈 Shining Will 管理画面 カスタムテーマ ===== */

                        /* サイドバー */
                        .fi-sidebar {
                            background-color: #f8f9fa !important;
                        }

                        /* 上部バー */
                        .fi-topbar {
                            background-color: #ffffff !important;
                            border-bottom: 1px solid #e0e0e0 !important;
                        }

                        /* プライマリボタン（青系） */
                        .fi-btn-primary {
                            background-color: #2196f3 !important;
                            border-color: #2196f3 !important;
                            color: white !important;
                            border-radius: 4px !important;
                            font-weight: 500 !important;
                        }

                        .fi-btn-primary:hover {
                            background-color: #1976d2 !important;
                        }

                        /* テーブル背景 */
                        .fi-ta-table {
                            background-color: #ffffff !important;
                        }

                        /* 日本語フォント適用 */
                        body {
                            font-family: "Noto Sans JP", "Hiragino Kaku Gothic ProN", Meiryo, sans-serif !important;
                        }

                        /* リンクカラー */
                        a {
                            color: #1976d2 !important;
                        }
                    </style>
                    HTML
                );
            },
        ],
    ],
];
