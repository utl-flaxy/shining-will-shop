<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">

    <!-- ✅ Square用 CSP 許可 -->
    <meta http-equiv="Content-Security-Policy"
          content="
            default-src 'self';
            script-src 'self' 'unsafe-inline' https://sandbox.web.squarecdn.com https://web.squarecdn.com https://cdn.jsdelivr.net;
            style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;
            img-src 'self' data:;
            connect-src 'self' https://sandbox.web.squarecdn.com https://web.squarecdn.com;
            frame-src https://sandbox.web.squarecdn.com https://web.squarecdn.com;
          ">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shining Will Shop')</title>

    @vite('resources/css/app.css')

    {{-- ✅ Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
</head>

<body class="font-sans bg-[#fcfcfc] text-gray-900">

    {{-- =========================
        ✅ ヘッダー
    ========================= --}}
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">

            {{-- ✅ ロゴ --}}
            <a href="{{ route('store.home') }}" class="text-xl font-light tracking-widest">
                Shining Will
            </a>

            {{-- ✅ ナビゲーション（HOME / ITEM / CATEGORY のみ） --}}
            <nav class="space-x-8 text-sm uppercase tracking-wider text-gray-700">

                <a href="{{ route('store.home') }}"
                   class="hover:text-pink-500 transition">
                    HOME
                </a>

                <a href="{{ route('store.index') }}"
                   class="hover:text-pink-500 transition">
                    ITEM
                </a>

                <a href="{{ route('store.home') }}#categories"
                   class="hover:text-pink-500 transition">
                    CATEGORY
                </a>

            </nav>
        </div>
    </header>

    {{-- =========================
        ✅ ページコンテンツ
    ========================= --}}
    <main>
        @yield('content')
    </main>

    {{-- =========================
        ✅ フッター
    ========================= --}}
    <footer class="bg-gray-100 mt-16 py-10 text-center text-sm text-gray-500">

        <p class="mb-3">
            © {{ date('Y') }} Shining Will. All rights reserved.
        </p>

        <div class="space-x-6">
            <a href="https://twitter.com/" target="_blank" class="hover:text-gray-800 transition">
                Twitter
            </a>
            <a href="https://youtube.com/" target="_blank" class="hover:text-gray-800 transition">
                YouTube
            </a>
        </div>

    </footer>

    {{-- ✅ Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
