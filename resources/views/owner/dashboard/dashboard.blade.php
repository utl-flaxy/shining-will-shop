@extends('owner.layouts.app')

@section('title', 'ダッシュボード')
@section('page-title', 'ダッシュボード')

@section('content')
    <div class="owner-cards">
        <div class="owner-card">
            <div class="owner-card-label">商品数</div>
            <div class="owner-card-value">{{ $totalProducts }}</div>
        </div>
        <div class="owner-card">
            <div class="owner-card-label">注文数</div>
            <div class="owner-card-value">{{ $totalOrders }}</div>
        </div>
    </div>

    <h2 class="owner-section-title">最近の注文</h2>

    <div class="owner-table-wrapper">
        <table class="owner-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>注文番号</th>
                <th>購入者</th>
                <th>合計金額</th>
                <th>ステータス</th>
                <th>更新日</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ number_format($order->total_amount ?? 0) }} 円</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->updated_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('owner.orders.show', $order) }}" class="owner-link">
                            詳細
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="owner-table-empty">まだ注文はありません。</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
