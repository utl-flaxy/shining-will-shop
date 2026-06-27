<?php

use Illuminate\Support\Facades\Route;

// =========================
// 🏪 フロント（ショップ側）
// =========================
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// ✅ Square 本番専用（※0円テスト決済では使用しない）
use App\Http\Controllers\Front\SquareCheckoutController;

// =========================
// 🔐 オーナー管理画面（完全自作）
// =========================
use App\Http\Controllers\Owner\AuthController as OwnerAuthController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ProductController as OwnerProductController;
use App\Http\Controllers\Owner\OrderController as OwnerOrderController;

// ✅ CSVエクスポート
use App\Http\Controllers\Admin\OrderExportController;

/*
|--------------------------------------------------------------------------
| 🏠 フロント（ショップ）
|--------------------------------------------------------------------------
*/

// ✅ スマホ特化トップ（新UI）
Route::get('/', [StoreController::class, 'home'])->name('store.home');

// ✅ 商品一覧
Route::get('/store', [StoreController::class, 'index'])->name('store.index');

// ✅ 商品詳細
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('products.show');

// ✅ カテゴリ別商品一覧
Route::get('/categories/{id}', [StoreController::class, 'category'])
    ->name('store.categories');

// =========================
// 🛒 カート
// =========================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// =========================
// 💳 チェックアウト（0円テスト決済 & 本番共通）
// ✅ ここは App\Http\Controllers\CheckoutController のみ使用
// =========================
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/start', [CheckoutController::class, 'start'])->name('checkout.start');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// =========================
// 💳 Square 本番決済専用（※今回は0円テストでは使用しない）
// =========================
Route::post('/square/checkout', [SquareCheckoutController::class, 'checkout'])
    ->name('square.checkout');

Route::get('/square/success', [SquareCheckoutController::class, 'success'])
    ->name('square.success');

Route::get('/square/cancel', [SquareCheckoutController::class, 'cancel'])
    ->name('square.cancel');

/*
|--------------------------------------------------------------------------
| 🔐 /owner 管理画面（完全自作）
|--------------------------------------------------------------------------
*/

Route::prefix('owner')->name('owner.')->group(function () {

    /*
    |-------------------------
    | ✅ ログイン（★ミドルウェア絶対につけない）
    |-------------------------
    */
    Route::get('/login', [OwnerAuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [OwnerAuthController::class, 'login'])
        ->name('login.post');

    /*
    |-------------------------
    | ✅ ログイン後のみアクセス可
    |-------------------------
    */
    Route::middleware('owner.auth')->group(function () {

        Route::post('/logout', [OwnerAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/', [OwnerDashboardController::class, 'index'])
            ->name('dashboard');

        // 商品管理
        Route::get('/products', [OwnerProductController::class, 'index'])
            ->name('products.index');

        Route::get('/products/create', [OwnerProductController::class, 'create'])
            ->name('products.create');

        Route::post('/products', [OwnerProductController::class, 'store'])
            ->name('products.store');

        Route::get('/products/{product}/edit', [OwnerProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/products/{product}', [OwnerProductController::class, 'update'])
            ->name('products.update');

        Route::delete('/products/{product}', [OwnerProductController::class, 'destroy'])
            ->name('products.destroy');

        // 注文管理
        Route::get('/orders', [OwnerOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [OwnerOrderController::class, 'show'])
            ->name('orders.show');

        Route::put('/orders/{order}/status', [OwnerOrderController::class, 'updateStatus'])
            ->name('orders.status.update');
    });
});

/*
|--------------------------------------------------------------------------
| ✅ CSVエクスポート（Filament & 直URL 共通）
|--------------------------------------------------------------------------
| URL: http://127.0.0.1/admin/orders/export/csv
| ルート名: admin.orders.export.csv
*/

Route::get(
    '/admin/orders/export/csv',
    [OrderExportController::class, 'exportCsv']
)->name('admin.orders.export.csv');
