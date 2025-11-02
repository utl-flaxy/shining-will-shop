@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="row">
  <div class="col-md-6">
    @if(!empty($product->images) && is_array($product->images) && count($product->images))
      <img src="{{ $product->images[0] }}" class="img-fluid" alt="{{ $product->name }}">
    @else
      <div class="border bg-light p-5 text-center">No image</div>
    @endif
  </div>
  <div class="col-md-6">
    <h1>{{ $product->name }}</h1>
    <p class="text-muted">{{ number_format($product->price) }}円</p>
    <p>{{ $product->description }}</p>
    <p>在庫: {{ $product->stock }}</p>

    <form method="POST" action="{{ route('cart.add') }}" class="mt-3">
      @csrf
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <div class="mb-2">
        <label for="qty" class="form-label">数量</label>
        <input id="qty" name="quantity" type="number" min="1" value="1" class="form-control" style="width:120px;">
      </div>
      <button class="btn btn-success">カートに入れる</button>
      <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">一覧に戻る</a>
    </form>
  </div>
</div>
@endsection
