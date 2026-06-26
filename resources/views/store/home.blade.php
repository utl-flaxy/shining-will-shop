@extends('layouts.app')

@section('title', 'Shining Will Shop')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white via-slate-50 to-slate-100">

    {{-- =========================
        🔶 ヒーロー（メインビジュアル）
    ========================= --}}
    <section class="max-w-6xl mx-auto px-4 pt-10 pb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">

            {{-- 左：テキスト --}}
            <div class="space-y-5 max-w-xl">
                <p class="inline-flex items-center text-xs tracking-[0.2em] uppercase text-pink-500 bg-pink-50 px-3 py-1 rounded-full">
                    Bety / Shining-Will Official Shop
                </p>

                <h1 class="text-2xl md:text-4xl font-semibold leading-relaxed text-slate-900">
                    推しのグッズを、<br>
                    <span class="bg-gradient-to-r from-pink-500 to-sky-500 bg-clip-text text-transparent">
                        いちばん近い場所
                    </span>
                    で。
                </h1>

                <p class="text-sm md:text-base text-slate-600 leading-relaxed">
                    Bety を中心とした Shining-Will 所属アイドルの
                    オフィシャルオンラインショップです。<br>
                    ライブ会場での出会いを、そのままあなたの手元に。
                </p>

                <div class="flex flex-col sm:flex-row gap-3 pt-3">
                    <a href="{{ route('store.index') }}"
                       class="inline-flex items-center justify-center px-6 py-3 rounded-full text-sm font-semibold
                              bg-pink-500 text-white shadow-md shadow-pink-200 hover:bg-pink-600 transition">
                        商品一覧を見る
                    </a>

                    <a href="#categories"
                       class="inline-flex items-center justify-center px-6 py-3 rounded-full text-sm font-semibold
                              bg-white/80 text-slate-700 border border-slate-200 hover:bg-slate-50 transition">
                        カテゴリーから探す
                    </a>
                </div>
            </div>

            {{-- 右：ヒーロー実画像 --}}
            <div class="flex-1">
                <div class="relative w-full max-w-sm mx-auto">
                    <img
                        src="{{ asset('images/top-visual.jpg') }}"
                        alt="Shining Will Main Visual"
                        class="w-full h-auto rounded-3xl shadow-xl object-cover"
                    >

                    {{-- フローティングカード --}}
                    <div class="hidden md:block absolute -bottom-4 -left-3 bg-white rounded-2xl shadow-lg px-4 py-3 text-xs">
                        <p class="font-semibold text-slate-800">本日もご来店ありがとうございます</p>
                        <p class="text-slate-500 mt-1">新作グッズや会場受け取り商品も順次追加予定です。</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- =========================
        🏷 カテゴリー一覧
    ========================= --}}
    <section id="categories" class="max-w-6xl mx-auto px-4 pb-12">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-sm font-semibold tracking-[0.2em] uppercase text-slate-500">
                Category
            </h2>
            <a href="{{ route('store.index') }}" class="text-xs text-slate-500 hover:text-slate-700">
                すべての商品 →
            </a>
        </div>

        @if(isset($categories) && $categories->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('store.categories', $category->id) }}"
                       class="bg-white border rounded-2xl overflow-hidden hover:shadow-lg transition">

                        <img
                            src="{{ $category->image
                                ? asset('storage/' . $category->image)
                                : asset('images/default-category.jpg') }}"
                            class="w-full h-40 object-cover"
                        >

                        <div class="p-4 text-center">
                            <div class="font-semibold text-sm mb-1">
                                {{ $category->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $category->products()->count() }} items
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400">カテゴリーは準備中です。</p>
        @endif
    </section>

    {{-- =========================
        🆕 新着商品
    ========================= --}}
    <section class="max-w-6xl mx-auto px-4 pb-16">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-sm font-semibold tracking-[0.2em] uppercase text-slate-500">
                New Items
            </h2>
            <a href="{{ route('store.index') }}" class="text-xs text-slate-500 hover:text-slate-700">
                一覧を見る →
            </a>
        </div>

        @php
            $items = (isset($newProducts) && $newProducts->count())
                ? $newProducts
                : (isset($products) ? $products->take(8) : collect());
        @endphp

        @if($items->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($items as $product)
                    @php
                        $mainImage = optional($product->images)->first();
                        $isSoldOut = method_exists($product, 'totalStock')
                            ? $product->totalStock() <= 0
                            : false;
                    @endphp

                    <a href="{{ route('products.show', $product) }}"
                       class="group block bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                        <div class="relative aspect-square bg-gray-100 overflow-hidden">
                            <img
                                src="{{ $mainImage
                                    ? asset('storage/' . $mainImage->url)
                                    : asset('images/no-image.png') }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition"
                            >

                            @if($isSoldOut)
                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                    <span class="text-white tracking-widest font-semibold text-sm">
                                        SOLD OUT
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-3">
                            <p class="text-sm text-gray-800 font-medium line-clamp-2 mb-1">
                                {{ $product->name }}
                            </p>
                            <p class="text-pink-600 font-semibold text-sm">
                                ¥{{ number_format($product->price) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400">
                新着アイテムは準備中です。
            </p>
        @endif
    </section>

</div>
@endsection
