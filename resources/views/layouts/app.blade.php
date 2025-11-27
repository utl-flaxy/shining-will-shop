<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shining Will Shop')</title>
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-[#fcfcfc] text-gray-900">

    {{-- ヘッダー --}}
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
            <a href="{{ route('store.index') }}" class="text-xl font-light tracking-widest">
                Shining Will
            </a>
            <nav class="space-x-6 text-sm uppercase tracking-wider">
                <a href="{{ route('store.index') }}" class="hover:underline">Home</a>
                <a href="{{ route('store.index') }}" class="hover:underline">Item</a>
                <a href="{{ route('store.index') }}" class="hover:underline">Category</a>
                <a href="/about" class="hover:underline">About</a>
            </nav>
        </div>
    </header>

    {{-- ページコンテンツ --}}
    <main>
        @yield('content')
    </main>

    {{-- フッター --}}
    <footer class="bg-gray-100 mt-16 py-10 text-center text-sm text-gray-500">
        <p>© {{ date('Y') }} Shining Will. All rights reserved.</p>
        <div class="mt-3 space-x-4">
            <a href="https://twitter.com/" target="_blank">Twitter</a>
            <a href="https://youtube.com/" target="_blank">YouTube</a>
        </div>
    </footer>

</body>
</html>
