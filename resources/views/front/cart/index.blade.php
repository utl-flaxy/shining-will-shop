<h1>Cart</h1>
@if(session('ok'))<div>{{ session('ok') }}</div>@endif
@if(empty($items))
  <p>カートは空です</p>
@else
<form method="post" action="{{ route('cart.update') }}">
@csrf
<table border="1" cellpadding="6">
  <tr><th>商品</th><th>数量</th><th>単価</th><th>小計</th><th></th></tr>
  @foreach($items as $line)
    <tr>
      <td>{{ $line['product']->name }}</td>
      <td><input type="number" name="lines[{{ $line['product']->id }}]" value="{{ $line['qty'] }}" min="1"></td>
      <td>¥{{ number_format($line['price']) }}</td>
      <td>¥{{ number_format($line['price'] * $line['qty']) }}</td>
      <td>
        <form method="post" action="{{ route('cart.remove') }}">
          @csrf
          <input type="hidden" name="product_id" value="{{ $line['product']->id }}">
          <button>削除</button>
        </form>
      </td>
    </tr>
  @endforeach
</table>
<button type="submit">数量を更新</button>
</form>

<p>小計: ¥{{ number_format($subtotal) }}</p>
<p>送料: ¥{{ number_format($shipping) }}</p>
<p><b>合計: ¥{{ number_format($total) }}</b></p>

<form method="post" action="{{ route('checkout.start') }}">
  @csrf
  <button type="submit">購入手続きへ</button>
</form>
@endif
