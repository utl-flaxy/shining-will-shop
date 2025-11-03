<h1>{{ $product->name }}</h1>
@php $imgs = $product->images ?? []; @endphp
<div style="display:flex;gap:12px;overflow:auto">
  @foreach($imgs as $img)
    <img src="{{ asset('storage/'.$img) }}" style="width:240px;height:240px;object-fit:cover">
  @endforeach
</div>
<p>¥{{ number_format($product->price) }}</p>
<form method="post" action="{{ route('cart.add') }}">
  @csrf
  <input type="hidden" name="product_id" value="{{ $product->id }}">
  <input type="number" name="qty" value="1" min="1">
  <button type="submit">カートに入れる</button>
</form>
<a href="{{ route('cart.index') }}">カートを見る</a>
