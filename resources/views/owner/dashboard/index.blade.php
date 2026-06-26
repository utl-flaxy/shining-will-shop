@extends('owner.layouts.app')

@section('title', 'ダッシュボード')
@section('page-title', 'ダッシュボード')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-xs text-slate-500 mb-1">登録商品数</div>
            <div class="text-2xl font-bold text-slate-800">{{ $totalProducts }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-xs text-slate-500 mb-1">注文数</div>
            <div class="text-2xl font-bold text-slate-800">{{ $totalOrders }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-xs text-slate-500 mb-1">ステータス</div>
            <div class="text-sm text-slate-700">開発環境 / ローカル</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-800">最近の注文</h2>
        </div>

        @if($recentOrders->isEmpty())
            <p class="text-xs text-slate-500">まだ注文はありません。</p>
        @else
            <table class="w-full text-xs border-t border-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-2 py-2">注文番号</th>
                        <th class="text-left px-2 py-2">購入者</th>
                        <th class="text-left px-2 py-2">金額</th>
                        <th class="text-left px-2 py-2">ステータス</th>
                        <th class="text-left px-2 py-2">日時</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr class="border-t border-slate-100">
                            <td class="px-2 py-2">{{ $order->order_number ?? $order->id }}</td>
                            <td class="px-2 py-2">{{ $order->customer_name ?? '-' }}</td>
                            <td class="px-2 py-2">{{ number_format($order->total_amount ?? 0) }} 円</td>
                            <td class="px-2 py-2 text-xs">
                                {{ $order->status ?? '-' }}
                            </td>
                            <td class="px-2 py-2">
                                {{ optional($order->created_at)->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
