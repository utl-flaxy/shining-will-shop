<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'オーナー管理画面') | Shining Will Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/owner.css') }}" rel="stylesheet">
</head>

<body>
<div class="owner-layout">

    <!-- 📌 サイドバー -->
    <aside class="owner-sidebar">
        <div class="owner-logo">
            <span class="owner-logo-main">Shining Will Shop</span>
            <span class="owner-logo-sub">OWNER</span>
        </div>

        <nav class="owner-nav">

            <a href="{{ route('owner.dashboard') }}"
               class="owner-nav-link {{ request()->routeIs('owner.dashboard') ? 'is-active' : '' }}">
                🏠 ダッシュボード
            </a>

            <a href="{{ route('owner.products.index') }}"
               class="owner-nav-link {{ request()->routeIs('owner.products.*') ? 'is-active' : '' }}">
                📦 商品管理
            </a>

            <a href="{{ route('owner.orders.index') }}"
               class="owner-nav-link {{ request()->routeIs('owner.orders.*') ? 'is-active' : '' }}">
                🛒 注文管理
            </a>

            {{-- 🚫 未実装のため完全削除（これがエラー原因だった）
            売上管理 / 顧客管理 / 設定
            --}}

        </nav>

        <form method="POST" action="{{ route('owner.logout') }}" class="owner-logout-form">
            @csrf
            <button type="submit" class="owner-logout-button">ログアウト</button>
        </form>
    </aside>

    <!-- 📌 メイン画面 -->
    <main class="owner-main">

        <header class="owner-header">
            <h1 class="owner-page-title">@yield('page-title', '管理画面')</h1>
            <div class="owner-header-right">
                <span class="owner-user-name">{{ auth()->user()->name ?? 'オーナー' }}</span>
            </div>
        </header>

        <section class="owner-content">

            @if (session('status'))
                <div class="owner-alert success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="owner-alert error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </section>

    </main>
</div>
</body>
</html>
