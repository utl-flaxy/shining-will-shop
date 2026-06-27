@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-5">

    @php
        // ✅ Filament保存画像（products.image）を直接使う
        $imageUrl = $product->main_image_url;

        // ✅ 在庫判定も既存ロジックに統一
        $isSoldOut = $product->totalStock() <= 0;
    @endphp

    <div class="mb-4">
        <img src="{{ $imageUrl }}" class="w-full max-w-md rounded shadow">
    </div>

    <h1 class="text-2xl font-bold mb-2">{{ $product->name }}</h1>

    <p class="text-lg mb-1 {{ $isSoldOut ? 'text-gray-400' : 'text-black' }}">
        ¥{{ number_format($product->price) }}
    </p>

    <p class="text-gray-700 mb-4">
        {{ $product->description }}
    </p>

    {{-- ========================
        ✅ カート追加フォーム
    ======================== --}}
    <form method="POST" action="{{ route('cart.add', $product) }}">
        @csrf

        @if($product->variants->count() > 0)
            <div class="mb-3">
                <label class="block mb-1">メンバーを選択</label>

                <select name="variant_id"
                        class="border rounded px-3 py-2 w-full"
                        {{ $isSoldOut ? 'disabled' : '' }}>
                    <option value="">選択してください</option>

                    @foreach($product->variants as $variant)
                        @if($variant->stock > 0)
                            <option value="{{ $variant->id }}">
                                {{ $variant->member_name }}
                            </option>
                        @else
                            <option disabled>
                                {{ $variant->member_name }}（売り切れ）
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif

        <div class="mb-3 w-32">
            <label class="block mb-1">数量</label>
            <input type="number"
                   name="quantity"
                   class="border rounded px-3 py-2 w-full"
                   value="1"
                   min="1"
                   {{ $isSoldOut ? 'disabled' : '' }}>
        </div>

        @if($isSoldOut)
            <button class="bg-gray-400 text-white px-6 py-2 rounded" disabled>
                SOLD OUT
            </button>
        @else
            <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                カートに入れる
            </button>
        @endif
    </form>

</div>
@endsection
