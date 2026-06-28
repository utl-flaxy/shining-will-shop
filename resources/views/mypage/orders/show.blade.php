@extends('layouts.app')

@section('title', '注文詳細')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-12">

    <div class="flex items-center justify-between mb-8">

        <div>

            <h1 class="text-3xl font-bold">
                注文詳細
            </h1>

            <p class="text-gray-500 mt-2">
                注文番号：{{ $order->order_number }}
            </p>

        </div>

        <a href="{{ route('mypage.orders') }}"
           class="text-blue-600 hover:text-blue-800 hover:underline">
            ← 注文履歴へ戻る
        </a>

    </div>

    {{-- 注文情報 --}}
    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <h2 class="text-xl font-semibold mb-5">
            注文情報
        </h2>

        <div class="grid grid-cols-2 gap-6">

            <div>

                <p class="text-gray-500 text-sm">
                    注文日時
                </p>

                <p class="font-semibold">
                    {{ $order->created_at->format('Y/m/d H:i') }}
                </p>

            </div>

            <div>

                <p class="text-gray-500 text-sm">
                    注文ステータス
                </p>

                <span class="inline-block px-3 py-1 rounded-full bg-gray-100">

                    {{ $order->status_label }}

                </span>

            </div>

            <div>

                <p class="text-gray-500 text-sm">
                    支払い方法
                </p>

                <p>
                    {{ $order->payment_method_label }}
                </p>

            </div>

            <div>

                <p class="text-gray-500 text-sm">
                    配送方法
                </p>

                <p>
                    {{ $order->delivery_method_label }}
                </p>

            </div>

            <div>

                <p class="text-gray-500 text-sm">
                    合計金額
                </p>

                <p class="text-2xl font-bold text-pink-600">

                    ¥{{ number_format($order->total_amount) }}

                </p>

            </div>

        </div>

        @if($order->note_to_talent)

            <div class="mt-8">

                <p class="text-gray-500 text-sm mb-2">
                    応援メッセージ
                </p>

                <div class="bg-gray-50 rounded-lg p-4">

                    {{ $order->note_to_talent }}

                </div>

            </div>

        @endif

    </div>

    {{-- 商品一覧 --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="px-6 py-5 border-b">

            <h2 class="text-xl font-semibold">
                購入商品
            </h2>

        </div>

        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="text-left p-4">
                        商品名
                    </th>

                    <th class="text-center p-4">
                        メンバー
                    </th>

                    <th class="text-right p-4">
                        単価
                    </th>

                    <th class="text-center p-4">
                        数量
                    </th>

                    <th class="text-right p-4">
                        小計
                    </th>

                </tr>

            </thead>

            <tbody>

            @foreach($order->items as $item)

                <tr class="border-t">

                    <td class="p-4">

                        <div class="font-semibold">

                            {{ $item->product_name }}

                        </div>

                    </td>

                    <td class="text-center p-4">

                        {{ $item->member_name ?? '-' }}

                    </td>

                    <td class="text-right p-4">

                        ¥{{ number_format($item->unit_price) }}

                    </td>

                    <td class="text-center p-4">

                        {{ $item->quantity }}

                    </td>

                    <td class="text-right p-4 font-semibold">

                        ¥{{ number_format($item->subtotal) }}

                    </td>

                </tr>

            @endforeach

            </tbody>

            <tfoot>

                <tr class="border-t bg-gray-50">

                    <td colspan="4"
                        class="text-right font-bold p-4">

                        合計

                    </td>

                    <td class="text-right font-bold text-xl p-4">

                        ¥{{ number_format($order->total_amount) }}

                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

</div>

@endsection
