<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Shining Will Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ✅ 旧CSSは完全に排除 --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/shop/theme.css') }}"> ← 削除済み --}}

    {{-- ✅ Viteのみ使用（これが正解） --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    {{-- 🔹 ヘッダー --}}
    <header class="border-b bg-white">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">

            {{-- ロゴ --}}
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Shining Will" class="h-10">
                <span class="font-semibold tracking-wide text-gray-800">
                    Shining Will
                </span>
            </div>

            {{-- ナビ --}}
            <nav class="hidden md:flex items-center gap-8 text-sm tracking-wide">
                <a href="{{ route('store.index') }}" class="text-gray-700 hover:text-black">HOME</a>
                <a href="{{ route('store.index') }}" class="text-gray-700 hover:text-black">ITEM</a>
                <a href="{{ route('store.index') }}" class="text-gray-700 hover:text-black">CATEGORY</a>
                <a href="{{ route('store.index') }}" class="text-gray-700 hover:text-black">ABOUT</a>
                <a href="{{ route('store.index') }}" class="text-gray-700 hover:text-black">SALE</a>
            </nav>

            {{-- 右側 --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('owner.login') }}"
                   class="text-xs px-4 py-2 border rounded hover:bg-black hover:text-white transition">
                    OWNER LOGIN
                </a>

                <a href="{{ route('cart.index') }}"
                   class="text-xl hover:opacity-70 transition">
                    🛒
                </a>
            </div>
        </div>
    </header>

    {{-- 🔹 メイン --}}
    <main class="min-h-screen bg-white">
        @yield('content')
    </main>

    {{-- 🔹 フッター --}}
    <footer class="border-t py-8 text-center text-xs text-gray-500">
        © 2025 Shining Will Shop
    </footer>

</body>
</html>
