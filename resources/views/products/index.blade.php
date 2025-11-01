@extends('layouts.app')

@section('title','商品一覧')

@section('content')
  <h2>商品一覧</h2>

  <div class="grid">
    @foreach($products as $product)
      <div class="card">
        @php
          $img = (is_array($product->images) && count($product->images)) ? $product->images[0] : null;
        @endphp

        @if($img)
          <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" class="thumb">
        @else
          <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:#9ca3af">No Image</div>
        @endif

        <h3 style="margin:8px 0 4px">{{ $product->name }}</h3>
        <div class="small-muted">¥{{ number_format((int)$product->price) }}</div>
        <p class="small-muted" style="margin:6px 0">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>

        <div style="margin-top:8px; display:flex; gap:8px; align-items:center">
          <a href="{{ route('products.show', $product) }}" class="btn" style="background:#111827">詳細</a>

          <form method="post" action="{{ route('cart.add') }}" class="inline" style="margin-left:auto">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="qty" value="1">
            <button type="submit" class="btn">カートに入れる</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>

  <div style="margin-top:18px">
    {{ $products->withQueryString()->links() }}
  </div>
@endsection
