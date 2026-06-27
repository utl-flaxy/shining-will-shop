@extends('owner.layouts.app')

@section('title', '注文詳細')
@section('page-title', '注文詳細')

@section('content')

<div class="owner-card">
    <div class="owner-card-header">
        <h2 class="owner-card-title">注文情報</h2>

        <a href="{{ route('owner.orders.index') }}" class="owner-button secondary small">
            ← 注文一覧へ戻る
        </a>
    </div>

    <div class="owner-detail-grid">

        <div class="owner-detail-item">
            <label>注文番号</label>
            <div>{{ $order->order_number }}</div>
        </div>

        <div class="owner-detail-item">
            <label>購入者名</label>
            <div>{{ $order->customer_name }}</div>
        </div>

        <div class="owner-detail-item">
            <label>メールアドレス</label>
            <div>{{ $order->customer_email }}</div>
        </div>

        <div class="owner-detail-item">
            <label>合計金額</label>
            <div>¥{{ number_format($order->total_amount) }}</div>
        </div>

        <div class="owner-detail-item">
            <label>ステータス</label>
            <div>
                <span class="owner-badge status-{{ $order->status }}">
                    {{ match($order->status) {
                        'pending'  => '入金待ち',
                        'paid'     => '入金済み',
                        'shipped'  => '発送済み',
                        'refunded' => '返金済み',
                        default    => '不明',
                    } }}
                </span>
            </div>
        </div>

        <div class="owner-detail-item">
            <label>注文日</label>
            <div>{{ $order->created_at->format('Y-m-d H:i') }}</div>
        </div>

        <div class="owner-detail-item">
            <label>発送日時</label>
            <div>{{ $order->shipped_at ? $order->shipped_at->format('Y-m-d H:i') : '未発送' }}</div>
        </div>

        <div class="owner-detail-item wide">
            <label>配送先住所</label>
            <div>{{ $order->shipping_address }}</div>
        </div>
    </div>
</div>


{{-- ========================= --}}
{{-- 購入商品一覧               --}}
{{-- ========================= --}}
<div class="owner-card mt-4">
    <div class="owner-card-header">
        <h2 class="owner-card-title">購入商品</h2>
    </div>

    <div class="owner-table-wrapper">
        <table class="owner-table">
            <thead>
                <tr>
                    <th>商品名</th>
                    <th>単価</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? '商品削除済み' }}</td>
                        <td>¥{{ number_format($item->price) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>¥{{ number_format($item->price * $item->quantity) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


{{-- ========================= --}}
{{-- アクションボタン          --}}
{{-- ========================= --}}
<div class="owner-card mt-4">
    <div class="owner-card-header">
        <h2 class="owner-card-title">アクション</h2>
    </div>

    <div class="owner-action-buttons">

        {{-- 入金確認 --}}
        @if ($order->status === 'pending')
            <form action="{{ route('owner.orders.confirmPayment', $order->id) }}" method="POST">
                @csrf
                <button class="owner-button success large">入金確認する</button>
            </form>
        @endif

        {{-- 発送処理 --}}
        @if ($order->status === 'paid')
            <form action="{{ route('owner.orders.ship', $order->id) }}" method="POST" class="mt-2">
                @csrf
                <button class="owner-button primary large">発送済みにする</button>
            </form>
        @endif

        {{-- 返金処理 --}}
        @if ($order->status === 'paid' || $order->status === 'shipped')
            <form action="{{ route('owner.orders.refund', $order->id) }}" method="POST" class="mt-2">
                @csrf
                <button class="owner-button danger large">返金する</button>
            </form>
        @endif
    </div>
</div>

@endsection
