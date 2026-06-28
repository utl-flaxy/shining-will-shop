@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        {{-- 左：画像 --}}
        <div class="relative">
            @php $isSoldOut = $product->totalStock() <= 0; @endphp

            <div class="swiper mySwiper rounded-xl shadow bg-black">
                <div class="swiper-wrapper">
                    @forelse($product->images as $image)
                        <div class="swiper-slide flex items-center justify-center">
                            <img src="{{ Storage::url($image->url) }}"
                                 class="w-full max-h-[720px] object-contain">
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <img src="{{ asset('images/no-image.png') }}">
                        </div>
                    @endforelse
                </div>

                <div class="swiper-pagination"></div>
            </div>

            @if($isSoldOut)
                <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-3xl font-bold">
                    SOLD OUT
                </div>
            @endif
        </div>

        {{-- 右：情報 --}}
        <div>
            <h1 class="text-3xl font-semibold mb-3">{{ $product->name }}</h1>
            <p class="text-xl mb-4">¥{{ number_format($product->price) }}</p>

            <p class="text-gray-600 mb-6">{{ $product->description }}</p>

            <form method="POST" action="{{ route('cart.add', $product) }}" class="space-y-4">
                @csrf

                <input type="number" name="quantity" value="1" min="1" class="w-32 border p-2">

                @if($isSoldOut)
                    <button class="w-full bg-gray-400 py-3 text-white" disabled>SOLD OUT</button>
                @else
                    {{-- ✅ 黒ボタン --}}
                    <button class="w-full bg-black text-white py-3 hover:bg-gray-800 transition">
                        カートに入れる
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>

{{-- Swiper --}}
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.swiper-button-prev,
.swiper-button-next {
    display: none !important;
}
</style>

<script>
new Swiper('.mySwiper', {
    loop: true,
    pagination: { el: '.swiper-pagination', clickable: true },
});
</script>
@endsection
