@extends('layouts.app')

@section('title', $product->name)

@section('content')

<div class="max-w-6xl mx-auto px-4 py-10">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        {{-- ===========================
            商品画像
        ============================ --}}
        <div class="relative">

            @php
                $isSoldOut = $product->totalStock() <= 0;
            @endphp

            <div class="swiper mySwiper rounded-xl shadow bg-black">

                <div class="swiper-wrapper">

                    @forelse($product->images as $image)

                        <div class="swiper-slide flex items-center justify-center">

                            <img
                                src="{{ asset('storage/' . ltrim($image->url, '/')) }}"
                                alt="{{ $product->name }}"
                                class="w-full max-h-[720px] object-contain"
                            >

                        </div>

                    @empty

                        <div class="swiper-slide flex items-center justify-center">

                            <img
                                src="{{ asset('images/no-image.png') }}"
                                alt="No Image"
                                class="w-full max-h-[720px] object-contain"
                            >

                        </div>

                    @endforelse

                </div>

                <div class="swiper-pagination"></div>

            </div>

            @if($isSoldOut)

                <div
                    class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-3xl font-bold"
                >
                    SOLD OUT
                </div>

            @endif

        </div>

        {{-- ===========================
            商品情報
        ============================ --}}
        <div>

            <h1 class="text-3xl font-semibold mb-3">
                {{ $product->name }}
            </h1>

            <p class="text-2xl font-bold text-pink-600 mb-5">
                ¥{{ number_format($product->price) }}
            </p>

            <p class="text-gray-600 whitespace-pre-line mb-8">
                {{ $product->description }}
            </p>

            <form
                method="POST"
                action="{{ route('cart.add', $product) }}"
                class="space-y-4"
            >

                @csrf

                @if($product->variants->count())

                    <div>

                        <label class="block mb-2 font-medium">
                            メンバー
                        </label>

                        <select
                            name="variant_id"
                            class="w-full border rounded px-3 py-2"
                            {{ $isSoldOut ? 'disabled' : '' }}
                        >

                            @foreach($product->variants as $variant)

                                <option
                                    value="{{ $variant->id }}"
                                    {{ $variant->stock <= 0 ? 'disabled' : '' }}
                                >
                                    {{ $variant->member_name }}

                                    @if($variant->stock <= 0)
                                        （売り切れ）
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>

                @endif

                <div>

                    <label class="block mb-2 font-medium">
                        数量
                    </label>

                    <input
                        type="number"
                        name="quantity"
                        value="1"
                        min="1"
                        class="w-32 border rounded px-3 py-2"
                        {{ $isSoldOut ? 'disabled' : '' }}
                    >

                </div>

                @if($isSoldOut)

                    <button
                        type="button"
                        class="w-full bg-gray-400 text-white py-3 rounded cursor-not-allowed"
                        disabled
                    >
                        SOLD OUT
                    </button>

                @else

                    <button
                        type="submit"
                        class="w-full bg-black hover:bg-gray-800 text-white py-3 rounded transition"
                    >
                        カートに入れる
                    </button>

                @endif

            </form>

        </div>

    </div>

</div>

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.swiper-button-prev,
.swiper-button-next{
    display:none!important;
}
</style>

<script>
new Swiper('.mySwiper', {
    loop: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
});
</script>

@endsection
