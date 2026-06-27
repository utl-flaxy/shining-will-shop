@extends('owner.layouts.app')

@section('title', '注文管理')
@section('page-title', '注文管理')

@section('content')

<div class="owner-card">
    <div class="owner-card-header">
        <h2 class="owner-card-title">注文一覧</h2>
        <a href="{{ route('owner.orders.export') }}" class="owner-button secondary">
            Excelエクスポート
        </a>
    </div>

    <div class="owner-table-wrapper">
        <table class="owner-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>注文番号</th>
                    <th>購入者名</th>
                    <th>メール</th>
                    <th>合計金額</th>
                    <th>ステータス</th>
                    <th>注文日</th>
                    <th class="text-right">操作</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_email }}</td>
                        <td>¥{{ number_format($order->total_amount) }}</td>

                        <td>
                            <span class="owner-badge status-{{ $order->status }}">
                                {{ match($order->status) {
                                    'pending'  => '入金待ち',
                                    'paid'     => '入金済み',
                                    'shipped'  => '発送済み',
                                    'refunded' => '返金済み',
                                    default    => '不明',
                                } }}
                            </span>
                        </td>

                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>

                        <td class="text-right">
                            <a href="{{ route('owner.orders.show', $order->id) }}"
                               class="owner-button small">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            注文はありません
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ページネーション --}}
        <div class="owner-pagination">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@endsection
