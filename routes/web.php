<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Owner\AuthController as OwnerAuthController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ProductController as OwnerProductController;
use App\Http\Controllers\Owner\OrderController as OwnerOrderController;

// ---------------------------------------------------
// 🏠 ストア（トップ・商品一覧・詳細）
// ---------------------------------------------------
Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// ---------------------------------------------------
// 🏷 カテゴリ別表示
// ---------------------------------------------------
Route::get('/categories/{id}', [StoreController::class, 'category'])->name('store.categories');

// ---------------------------------------------------
// 🛒 カート機能
// ---------------------------------------------------
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// ---------------------------------------------------
// 💳 チェックアウト（ゲスト購入対応）
// ---------------------------------------------------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/start', [CheckoutController::class, 'start'])->name('checkout.start');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// ---------------------------------------------------
// 🔐 /owner 管理画面（自作）
// ---------------------------------------------------
Route::prefix('owner')->name('owner.')->group(function () {
    // 🔓 ログイン・ログアウト
    Route::get('/login', [OwnerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [OwnerAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [OwnerAuthController::class, 'logout'])->name('logout');

    // 💡 ログイン必須
    Route::middleware('owner.auth')->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');

        // 商品管理
        Route::get('/products', [OwnerProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}/edit', [OwnerProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [OwnerProductController::class, 'update'])->name('products.update');

        // 注文管理
        Route::get('/orders', [OwnerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OwnerOrderController::class, 'show'])->name('orders.show');
    });
});
