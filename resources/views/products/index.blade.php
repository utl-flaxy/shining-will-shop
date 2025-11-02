@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>商品一覧</h1>
  <a class="btn btn-outline-primary" href="{{ route('cart.index') }}">カートを見る</a>
</div>

<div class="row">
  @foreach($products as $product)
    <div class="col-md-4 mb-3">
      <div class="card product-card">
        @if(!empty($product->images) && is_array($product->images) && count($product->images))
          <img src="{{ $product->images[0] }}" class="card-img-top" alt="{{ $product->name }}" style="height:160px;object-fit:cover;">
        @endif
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">{{ $product->name }}</h5>
          <p class="card-text text-muted mb-2">{{ Str::limit($product->description, 80) }}</p>
          <p class="mb-2"><strong>{{ number_format($product->price) }}円</strong></p>
          <div class="mt-auto">
            <a href="{{ route('products.show', $product) }}" class="btn btn-primary btn-sm">詳細</a>

            <form method="POST" action="{{ route('cart.add') }}" class="d-inline-block">
              @csrf
              <input type="hidden" name="product_id" value="{{ $product->id }}">
              <input type="hidden" name="quantity" value="1">
              <button class="btn btn-outline-success btn-sm">カートに入れる</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
