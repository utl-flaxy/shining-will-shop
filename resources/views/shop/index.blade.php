@extends('layouts.app')

@section('content')
<div class="bg-pink-50 min-h-screen">

    {{-- 🌸 トップビジュアル --}}
    <section class="relative">
        <img src="{{ asset('images/top-visual.jpg') }}" alt="Top Visual"
             class="w-full h-72 object-cover brightness-95">
        <div class="absolute inset-0 bg-pink-400 bg-opacity-30 flex flex-col justify-center items-center text-white text-center">
            <h1 class="text-4xl font-bold tracking-wide drop-shadow-md">Shining Will Shop</h1>
            <p class="mt-2 text-lg">Shining Will グッズ販売サイト</p>
        </div>
    </section>

    {{-- 🗂 カテゴリ一覧 --}}
    <section class="max-w-6xl mx-auto px-6 py-12">
        <h2 class="text-2xl font-semibold text-pink-600 mb-6 border-b-2 border-pink-200 pb-2">カテゴリ一覧</h2>

        @if($categories->isEmpty())
            <p class="text-gray-500 text-center">現在、登録されているカテゴリはありません。</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('store.categories', $category->id) }}"
                       class="bg-white shadow-md rounded-lg hover:shadow-lg transition p-4 text-center border border-pink-100 hover:border-pink-300">
                        <div class="font-semibold text-gray-800">{{ $category->name }}</div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 🆕 新着商品 --}}
    <section class="max-w-6xl mx-auto px-6 pb-16">
        <h2 class="text-2xl font-semibold text-pink-600 mb-6 border-b-2 border-pink-200 pb-2">新着商品</h2>

        @if($newProducts->isEmpty())
            <p class="text-gray-500 text-center">新着商品はまだ登録されていません。</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($newProducts as $product)
                    <a href="{{ route('products.show', $product->id) }}"
                       class="bg-white shadow-md rounded-lg hover:shadow-lg transition overflow-hidden border border-pink-100 hover:border-pink-300">
                        <img src="{{ $product->image_url ?? asset('images/no-image.jpg') }}"
                             alt="{{ $product->name }}"
                             class="w-full h-48 object-cover">
                        <div class="p-4 text-center">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $product->name }}</h3>
                            <p class="text-pink-500 mt-1 font-medium">¥{{ number_format($product->price) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection
