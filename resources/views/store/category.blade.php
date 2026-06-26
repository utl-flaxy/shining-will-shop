@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="text-2xl md:text-3xl font-semibold mb-8">{{ $category->name }}</h1>

  @if($products->isEmpty())
    <div class="text-center text-gray-500 py-20">このカテゴリにはまだ商品がありません。</div>
  @else
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      @foreach($products as $product)
        @php
          // 先頭画像のパスを柔軟に取得する
          $getPath = function($img){
              if (!$img) return null;
              return $img->url ?? $img->path ?? $img->image_path ?? $img->filename ?? null;
          };

          $mainImage = $product->images->first();
          $mainPath = $getPath($mainImage);
          $mainUrl = $mainPath ? asset('storage/' . ltrim($mainPath, '/')) : asset('images/no-image.png');

          // サムネ用に最初の3件を取り出し、相対パス配列にする
          $thumbs = [];
          if ($product->images && count($product->images)) {
              $count = 0;
              foreach ($product->images as $img) {
                  if ($count >= 3) break;
                  $p = $getPath($img);
                  if ($p) {
                      $thumbs[] = $p;
                      $count++;
                  }
              }
          }
        @endphp

        <a href="{{ route('products.show', $product) }}" class="group block bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
          <div class="relative aspect-square bg-gray-100 overflow-hidden">
            <img src="{{ $mainUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition" />
          </div>

          <div class="p-3">
            <p class="text-sm text-gray-800 font-medium line-clamp-2 mb-1">{{ $product->name }}</p>
            <p class="text-pink-600 font-semibold text-sm">¥{{ number_format($product->price) }}</p>

            {{-- 小さなサムネ表示 --}}
            @if(count($thumbs))
              <div class="flex gap-2 mt-3">
                @foreach($thumbs as $t)
                  <img src="{{ asset('storage/' . ltrim($t, '/')) }}" alt="" class="w-12 h-12 object-cover rounded" />
                @endforeach
              </div>
            @endif
          </div>
        </a>
      @endforeach
    </div>

    {{ $products->links() }}
  @endif
</div>
@endsection
