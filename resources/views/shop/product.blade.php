@extends('layouts.app')

@section('content')
<div class="bg-pink-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-6 py-12 flex flex-col md:flex-row gap-8">
        <div class="flex-1">
            <img src="{{ $product->image_url ?? asset('images/no-image.jpg') }}" alt="{{ $product->name }}" class="rounded-lg shadow-md w-full object-cover">
        </div>
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-gray-800">{{ $product->name }}</h1>
            <p class="text-pink-500 text-2xl mt-3 mb-6 font-semibold">¥{{ number_format($product->price) }}</p>
            <p class="text-gray-700 leading-relaxed mb-6">{{ $product->description }}</p>

            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="bg-pink-500 text-white px-6 py-3 rounded-md hover:bg-pink-600 transition">
                    カートに追加
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
