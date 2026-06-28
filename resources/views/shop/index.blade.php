@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('title', '商品一覧 | Shining Will')

@section('content')

<section class="max-w-6xl mx-auto px-4 py-12">

    <h1 class="text-2xl font-semibold mb-8">
        商品一覧
    </h1>

    {{-- ==========================================
        検索フォーム
    =========================================== --}}
    <form
        method="GET"
        action="{{ route('store.index') }}"
        class="mb-10 grid gap-4 md:grid-cols-4"
    >

        {{-- キーワード --}}
        <div>
            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="商品名・説明で検索"
                class="w-full rounded border px-4 py-2"
            >
        </div>

        {{-- カテゴリ --}}
        <div>
            <select
                name="category"
                class="w-full rounded border px-4 py-2"
            >

                <option value="">
                    全カテゴリ
                </option>

                @foreach($categories as $category)

                    <option
                        value="{{ $category->id }}"
                        @selected(request('category') == $category->id)
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>
        </div>

        {{-- 並び替え --}}
        <div>
            <select
                name="sort"
                class="w-full rounded border px-4 py-2"
            >

                <option value="">
                    新着順
                </option>

                <option
                    value="price_asc"
                    @selected(request('sort') === 'price_asc')
                >
                    価格が安い順
                </option>

                <option
                    value="price_desc"
                    @selected(request('sort') === 'price_desc')
                >
                    価格が高い順
                </option>

                <option
                    value="name"
                    @selected(request('sort') === 'name')
                >
                    名前順
                </option>

            </select>
        </div>

        {{-- ボタン --}}
        <div class="flex gap-2">

            <button
                type="submit"
                class="rounded bg-black px-5 py-2 text-white hover:bg-gray-800"
            >
                検索
            </button>

            <a
                href="{{ route('store.index') }}"
                class="rounded border px-5 py-2 hover:bg-gray-100"
            >
                リセット
            </a>

        </div>

    </form>

    {{-- ==========================================
        商品一覧
    =========================================== --}}

    @if($products->isEmpty())

        <div class="rounded border bg-white p-10 text-center text-gray-500">
            該当する商品が見つかりませんでした
        </div>

    @else

        <div class="grid grid-cols-2 gap-6 md:grid-cols-4">

            @foreach($products as $product)

                @php

                    $image = $product->images->first();

                    $imageUrl = $image
                        ? asset('storage/' . ltrim($image->url, '/'))
                        : asset('images/no-image.png');

                    $isSoldOut = $product->isSoldOut();

                @endphp

                <a
                    href="{{ route('products.show', $product) }}"
                    class="overflow-hidden rounded-xl bg-white shadow transition hover:shadow-lg"
                >

                    <div class="relative aspect-square bg-gray-100">

                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        >

                        @if($isSoldOut)

                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/60 text-lg font-bold text-white"
                            >
                                SOLD OUT
                            </div>

                        @endif

                    </div>

                    <div class="p-4">

                        <div class="font-medium">
                            {{ $product->name }}
                        </div>

                        <div class="mt-2 text-pink-600 font-bold">
                            ¥{{ number_format($product->price) }}
                        </div>

                        <div class="mt-1 text-xs text-gray-500">
                            {{ $product->category?->name ?? '未分類' }}
                        </div>

                    </div>

                </a>

            @endforeach

        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>

    @endif

</section>

@endsection
