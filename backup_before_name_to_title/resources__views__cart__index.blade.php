@extends('layouts.app')

@section('title','カート')

@section('content')
  <h2>カート</h2>

  @if($items->isEmpty())
    <p>カートに商品がありません。</p>
    <a href="{{ route('products.index') }}" class="btn">商品一覧に戻る</a>
  @else
    <form method="post" action="{{ route('cart.update') }}">
      @csrf
      <table class="cart" style="margin-top:12px">
        <thead>
          <tr>
            <th>商品</th>
            <th>単価</th>
            <th>数量</th>
            <th>小計</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $item)
            <tr>
              <td style="display:flex;gap:10px; align-items:center">
                @if($item['image'])
                  <img src="{{ asset('storage/' . $item['image']) }}" alt="" width="72" height="72" style="object-fit:cover; border-radius:6px">
                @endif
                <div>
                  <div style="font-weight:600">{{ $item['title'] ?? $item['name'] ?? '' }}</div>
                </div>
              </td>
              <td>¥{{ number_format($item['price']) }}</td>
              <td>
                <input type="number" name="qty" value="{{ $item['qty'] }}" min="0" data-product-id="{{ $item['product_id'] }}" style="width:80px; padding:6px;">
                <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                <div class="small-muted">0にすると削除されます</div>
              </td>
              <td>¥{{ number_format($item['subtotal']) }}</td>
              <td>
                <form method="post" action="{{ route('cart.remove') }}">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                  <button class="btn" style="background:#ef4444">削除</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center">
        <div>
          <button formaction="{{ route('cart.clear') }}" formmethod="post" class="btn" style="background:#ef4444">カートを空にする</button>
        </div>

        <div style="text-align:right">
          <div style="font-size:1.1rem; font-weight:700">合計：¥{{ number_format($total) }}</div>
          <div style="margin-top:8px">
            {{-- ここで決済への遷移（Stripe Checkout など）を実装します --}}
            <button type="submit" class="btn" style="margin-top:8px">カートを更新</button>
          </div>
        </div>
      </div>
    </form>
  @endif
@endsection
