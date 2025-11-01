@extends('layouts.app')

@section('title', $product->name)

@section('content')
  <a href="{{ route('products.index') }}" class="small-muted">← 商品一覧へ戻る</a>

  <div style="display:flex; gap:20px; margin-top:12px; align-items:flex-start">
    <div style="flex:0 0 360px">
      @php
        $images = is_array($product->images) ? $product->images : [];
      @endphp

      @if(count($images))
        <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $product->name }}" style="width:100%; border-radius:8px;">
        @if(count($images) > 1)
          <div style="display:flex; gap:8px; margin-top:8px">
            @foreach($images as $im)
              <img src="{{ asset('storage/' . $im) }}" width="72" height="72" style="object-fit:cover; border-radius:6px">
            @endforeach
          </div>
        @endif
      @else
        <div class="thumb" style="height:300px; display:flex;align-items:center;justify-content:center">No Image</div>
      @endif
    </div>

    <div style="flex:1">
      <h1 style="margin:0 0 6px">{{ $product->name }}</h1>
      <div class="small-muted">¥{{ number_format((int)$product->price) }}</div>
      <div style="margin-top:12px; color:#374151">{!! nl2br(e($product->description)) !!}</div>

      <form method="post" action="{{ route('cart.add') }}" style="margin-top:18px">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <label>
          数量：
          <input type="number" name="qty" value="1" min="1" style="width:80px; padding:6px; margin-left:6px;">
        </label>
        <div style="margin-top:12px">
          <button class="btn">カートに入れる</button>
        </div>
      </form>
    </div>
  </div>
@endsection
