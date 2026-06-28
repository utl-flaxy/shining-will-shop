@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

  <h1 class="text-2xl md:text-3xl font-semibold mb-8">
    {{ $category->name }}
  </h1>

  @if($products->isEmpty())
    <div class="text-center text-gray-500 py-20">
      このカテゴリにはまだ商品がありません。
    </div>
  @else

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

      @foreach($products as $product)

        @php
          // ✅ メイン画像1枚だけ取得（完全確定版）
          $mainImage = optional($product->images)->first();

          $mainUrl = $mainImage && $mainImage->url
            ? Storage::url($mainImage->url)
            : asset('images/no-image.png');
        @endphp

        <a href="{{ route('products.show', $product) }}"
           class="group block bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

          {{-- ✅ メイン画像（1枚のみ） --}}
          <div class="relative aspect-square bg-gray-100 overflow-hidden">
            <img
              src="{{ $mainUrl }}"
              alt="{{ $product->name }}"
              class="w-full h-full object-cover group-hover:scale-105 transition"
            >
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

    <div class="mt-10">
      {{ $products->links() }}
    </div>

  @endif
</div>
@endsection
