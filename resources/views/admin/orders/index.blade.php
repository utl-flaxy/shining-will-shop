@extends('layouts.admin')

@section('title', '注文一覧')

@section('content')
<div class="admin-card">
  <h2 class="card-title">注文一覧</h2>
  <p class="card-desc">受注データを確認・管理できます。</p>

  <table class="table-list">
    <thead>
      <tr>
        <th>注文番号</th>
        <th>購入者</th>
        <th>商品名</th>
        <th>金額</th>
        <th>ステータス</th>
        <th>注文日</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $order)
      <tr>
        <td>{{ $order->id }}</td>
        <td>{{ $order->customer->name }}</td>
        <td>{{ $order->product->name }}</td>
        <td>¥{{ number_format($order->total) }}</td>
        <td>
          <span class="status {{ $order->status }}">{{ $order->status_label }}</span>
        </td>
        <td>{{ $order->created_at->format('Y/m/d') }}</td>
        <td>
          <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-small">詳細</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
