@extends('layouts.app')

@section('title', 'カート')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>カート</h1>
  <a class="btn btn-outline-primary" href="{{ route('products.index') }}">買い物を続ける</a>
</div>

@if($cart && $cart->items->isNotEmpty())
  <table class="table">
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
      @php $total = 0; @endphp
      @foreach($cart->items as $item)
        @php $sub = $item->quantity * $item->product->price; $total += $sub; @endphp
        <tr>
          <td>{{ $item->product->name }}</td>
          <td>{{ number_format($item->product->price) }}円</td>
          <td>
            <form method="POST" action="{{ route('cart.update', $item->id) }}" class="d-inline-flex">
              @csrf
              <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" class="form-control form-control-sm" style="width:80px;">
              <button class="btn btn-sm btn-outline-secondary ms-2">更新</button>
            </form>
          </td>
          <td>{{ number_format($sub) }}円</td>
          <td>
            <form method="POST" action="{{ route('cart.remove', $item->id) }}">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger">削除</button>
            </form>
          </td>
        </tr>
      @endforeach
      <tr>
        <td colspan="3" class="text-end"><strong>合計</strong></td>
        <td><strong>{{ number_format($total) }}円</strong></td>
        <td></td>
      </tr>
    </tbody>
  </table>

  <div class="d-flex gap-2">
    <button class="btn btn-primary" onclick="startCheckout()">決済に進む</button>
    <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">買い物を続ける</a>
  </div>

@else
  <div class="alert alert-info">カートは空です。</div>
  <a class="btn btn-primary" href="{{ route('products.index') }}">商品一覧へ</a>
@endif

@endsection
