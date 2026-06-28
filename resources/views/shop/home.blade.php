@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('title', 'Shining Will Shop')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white via-slate-50 to-slate-100">

    {{-- =========================
        🔶 ロゴ帯（コンパクト版）
    ========================= --}}
    <section class="max-w-6xl mx-auto px-4 pt-10 pb-12 text-center">

        {{-- ✅ ロゴ縮小・横長バナー --}}
        <div class="relative w-full max-w-3xl mx-auto mb-6">
            <div class="absolute inset-0 bg-gradient-to-r from-pink-100 via-white to-sky-100 rounded-3xl blur-xl"></div>

            <img
                src="{{ asset('images/top-visual.jpg') }}"
                alt="Shining Will Official Logo"
                class="relative z-10 w-full h-auto rounded-3xl shadow-md object-contain bg-white px-6 py-5"
            >
        </div>

        {{-- ✅ シンプル説明のみ残す --}}
        <p class="text-sm text-slate-600">
            Shining-Will 公式オンラインショップ
        </p>

    </section>

    {{-- =========================
        🏷 カテゴリー一覧（管理画面 sort_order 完全一致）
    ========================= --}}
    <section id="categories" class="max-w-6xl mx-auto px-4 pb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-sm font-semibold tracking-[0.2em] uppercase text-slate-500">
                Category
            </h2>
        </div>

        @if(isset($categories) && $categories->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                @foreach($categories->sortBy('sort_order') as $category)

                    @if($category->is_active)
                        <a href="{{ route('store.categories', $category->id) }}"
                           class="bg-white border rounded-2xl overflow-hidden hover:shadow-lg transition">

                            <img
                                src="{{ $category->image
                                    ? Storage::disk('s3')->url($category->image)
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
                    @endif

                @endforeach

            </div>
        @else
            <p class="text-xs text-slate-400">カテゴリーは準備中です。</p>
        @endif
    </section>

    {{-- =========================
        🆕 新着商品
    ========================= --}}
    <section class="max-w-6xl mx-auto px-4 pb-20">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-sm font-semibold tracking-[0.2em] uppercase text-slate-500">
                New Items
            </h2>
            <a href="{{ route('store.index') }}" class="text-xs text-slate-500 hover:text-slate-700">
                商品一覧を見る →
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
                                    ? Storage::disk('s3')->url($mainImage->url)
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
