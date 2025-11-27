<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shining Will Shop</title>
    <link rel="stylesheet" href="{{ asset('css/shop/theme.css') }}">
</head>
<body>

    {{-- 🔹 ヘッダー --}}
    <header class="header">
        <div class="logo-area">
            <img src="{{ asset('images/logo.png') }}" alt="Shining Will" class="logo">
        </div>
        <nav class="nav">
            <a href="{{ route('store.index') }}">HOME</a>
            <a href="{{ route('store.index') }}">ITEM</a>
            <a href="{{ route('store.index') }}">CATEGORY</a>
            <a href="{{ route('store.index') }}">ABOUT</a>
            <a href="{{ route('store.index') }}">SALE</a>
        </nav>
        <div class="actions">
            <a href="{{ route('login') }}" class="btn-login">LOGIN</a>
            <a href="{{ route('cart.index') }}" class="btn-cart">🛒</a>
        </div>
    </header>

    {{-- 🔹 メインコンテンツ --}}
    <main class="main">
        @yield('content')
    </main>

    {{-- 🔹 フッター --}}
    <footer class="footer">
        <p>© 2025 Shining Will Shop</p>
    </footer>

</body>
</html>
