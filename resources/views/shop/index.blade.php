@extends('layouts.app')

@section('title', '商品一覧 | Shining Will')

@section('content')

<section class="max-w-6xl mx-auto px-4 py-12">

    <h1 class="text-xl font-semibold mb-8 tracking-wide">
        商品一覧
    </h1>

    @if($products->isEmpty())
        <p class="text-gray-500">現在販売中の商品はありません。</p>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

            @foreach($products as $product)

                @php
                    // ✅ 正しい画像取得方法（product_images の1枚目）
                    $imageUrl = $product->images->first()
                        ? asset('storage/' . $product->images->first()->url)
                        : asset('images/no-image.png');

                    $isSoldOut = $product->totalStock() <= 0;
                @endphp

                <a href="{{ route('products.show', $product->id) }}"
                   class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                    <div class="relative aspect-square bg-gray-100 overflow-hidden">
                        <img src="{{ $imageUrl }}"
                             class="w-full h-full object-cover">

                        @if($isSoldOut)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-sm font-semibold">
                                SOLD OUT
                            </div>
                        @endif
                    </div>

                    <div class="p-3">
                        <p class="text-sm font-medium">{{ $product->name }}</p>
                        <p class="text-pink-600 font-semibold mt-1">
                            ¥{{ number_format($product->price) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ optional($product->category)->name ?? '未分類' }}
                        </p>
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
