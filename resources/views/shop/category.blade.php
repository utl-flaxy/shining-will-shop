@extends('layouts.app')

@section('content')
<div class="bg-pink-50 min-h-screen">
    <section class="max-w-6xl mx-auto px-6 py-12">
        <h2 class="text-2xl font-bold text-pink-600 mb-8">{{ $category->name }}</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @forelse($products as $product)
                <a href="{{ route('products.show', $product->id) }}" class="bg-white shadow-md rounded-lg hover:shadow-lg transition overflow-hidden">
                    <img src="{{ $product->image_url ?? asset('images/no-image.jpg') }}" class="w-full h-48 object-cover">
                    <div class="p-4 text-center">
                        <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                        <p class="text-pink-500 mt-1">¥{{ number_format($product->price) }}</p>
                    </div>
                </a>
            @empty
                <p class="text-gray-500">このカテゴリにはまだ商品がありません。</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </section>
</div>
@endsection
