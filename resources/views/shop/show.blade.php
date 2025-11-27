@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4">{{ $product->name }}</h1>

        {{-- 商品画像 --}}
        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-64 mb-4 rounded">
        @else
            <div class="w-64 h-64 bg-gray-100 mb-4 flex items-center justify-center text-gray-400">
                No Image
            </div>
        @endif

        {{-- 価格 --}}
        <p class="text-xl font-semibold mb-2">価格: ¥{{ number_format($product->price) }}</p>

        {{-- カテゴリ --}}
        <p class="text-gray-600 mb-2">カテゴリ: {{ $product->category->name ?? '未分類' }}</p>

        {{-- 在庫 --}}
        <p class="text-gray-600 mb-4">在庫: {{ $product->stock }}</p>

        {{-- 詳細説明 --}}
        <p class="text-gray-700 leading-relaxed mb-6">
            {!! nl2br(e($product->description ?? '説明はありません。')) !!}
        </p>

        {{-- カートボタン（後でcheckoutへ連携） --}}
        <form action="{{ route('cart.add', $product->id) }}" method="POST">
            @csrf
            <button type="submit"
                class="bg-pink-500 hover:bg-pink-600 text-white font-semibold px-6 py-2 rounded transition duration-200">
                カートに追加
            </button>
        </form>
    </div>
</div>
@endsection
