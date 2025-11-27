<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shining-Will Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- ヘッダー --}}
    <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-6 py-3">
            <h1 class="text-xl font-bold text-gray-900">Shining-Will Shop</h1>
            <nav class="space-x-6 text-sm font-medium">
                <a href="#" class="hover:text-pink-500">ホーム</a>
                <a href="#" class="hover:text-pink-500">商品一覧</a>
                <a href="#" class="hover:text-pink-500">カート</a>
            </nav>
        </div>
    </header>

    {{-- メインビジュアル --}}
    <section class="relative w-full h-[60vh] bg-cover bg-center mt-16"
        style="background-image: url('{{ asset('images/hero_main.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col justify-center items-center text-white">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">輝きをあなたに。</h2>
            <p class="text-lg md:text-xl">アイドルたちの魅力を詰め込んだ公式ショップ</p>
        </div>
    </section>

    {{-- カテゴリ一覧 --}}
    <section class="max-w-6xl mx-auto py-12 px-6">
        <h2 class="text-2xl font-bold mb-8 text-gray-900">カテゴリー</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($categories as $category)
                <a href="{{ route('store.index', ['category' => $category->id]) }}"
                   class="bg-white rounded-2xl shadow hover:shadow-lg transition p-4 flex flex-col items-center">
                    <div class="w-full h-48 bg-gray-100 rounded-xl overflow-hidden">
                        @if ($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://placehold.jp/300x200.png?text={{ urlencode($category->name) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <h3 class="mt-4 text-lg font-semibold">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500">商品数: {{ $category->products_count }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 text-center py-6 mt-12">
        <p class="text-sm">&copy; {{ date('Y') }} Shining-Will. All Rights Reserved.</p>
    </footer>

</body>
</html>
