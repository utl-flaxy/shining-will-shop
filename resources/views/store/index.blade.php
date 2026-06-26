@extends('layouts.app')

@section('content')
    {{-- ✅ メインビジュアルセクション --}}
    <section class="relative w-full h-[70vh] flex items-center justify-center bg-gray-200 overflow-hidden">
        <img src="{{ asset('images/hero_main.jpg') }}" alt="Main Visual"
             class="absolute inset-0 w-full h-full object-cover opacity-90">
        <div class="relative z-10 text-center text-white drop-shadow-md">
            <h1 class="text-4xl md:text-5xl font-light tracking-widest mb-4">Shining Will</h1>
            <p class="text-sm md:text-base font-light">The official shop for Bety・てぃあむ・ソロタレント</p>
        </div>
        <div class="absolute inset-0 bg-black/40"></div>
    </section>

    {{-- ✅ カテゴリーカード一覧 --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <h2 class="text-2xl font-light tracking-widest text-center mb-12">CATEGORY</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            @forelse ($categories as $category)
                <div class="group relative overflow-hidden rounded-2xl shadow-md hover:shadow-lg transition">
                    {{-- 画像 --}}
                    <img
                        src="{{ $category->image ? asset('storage/' . $category->image) : asset('images/default-category.jpg') }}"
                        alt="{{ $category->name }}"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105"
                    >

                    {{-- オーバーレイ --}}
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/50 transition-all duration-300"></div>

                    {{-- カテゴリ名 --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <h3 class="text-white text-2xl font-semibold tracking-widest">
                            {{ $category->name }}
                        </h3>
                    </div>

                    {{-- ✅ カテゴリーページへのリンク --}}
                    <a href="{{ route('store.categories', $category->id) }}" class="absolute inset-0"></a>
                </div>
            @empty
                <p class="col-span-3 text-center text-gray-500">
                    カテゴリーがまだ登録されていません。
                </p>
            @endforelse
        </div>
    </section>
@endsection
