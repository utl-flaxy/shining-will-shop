@extends('layouts.app')

@section('title', '注文履歴')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-12">

    <div class="flex items-center justify-between mb-8">

        <div>
            <h1 class="text-3xl font-bold">
                注文履歴
            </h1>

            <p class="text-gray-500 mt-2">
                過去のご注文一覧です。
            </p>
        </div>

        <a href="{{ route('mypage') }}"
           class="text-blue-600 hover:text-blue-800 hover:underline">
            ← マイページへ戻る
        </a>

    </div>

    @if($orders->isEmpty())

        <div class="bg-white rounded-xl shadow p-12 text-center">

            <div class="text-6xl mb-4">
                📦
            </div>

            <h2 class="text-xl font-semibold mb-2">
                注文履歴はありません
            </h2>

            <p class="text-gray-500 mb-8">
                商品を購入するとここへ表示されます。
            </p>

            <a href="{{ route('store.index') }}"
               class="inline-block bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-lg transition">

                商品一覧を見る

            </a>

        </div>

    @else

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">

                <tr>

                    <th class="p-4 text-left">
                        注文番号
                    </th>

                    <th class="p-4 text-left">
                        注文日
                    </th>

                    <th class="p-4 text-right">
                        合計金額
                    </th>

                    <th class="p-4 text-center">
                        ステータス
                    </th>

                    <th class="p-4 text-center">
                        詳細
                    </th>

                </tr>

                </thead>

                <tbody>

                @foreach($orders as $order)

                    <tr class="border-t hover:bg-gray-50 transition">

                        <td class="p-4 font-medium">
                            {{ $order->order_number }}
                        </td>

                        <td class="p-4">
                            {{ $order->created_at->format('Y/m/d H:i') }}
                        </td>

                        <td class="p-4 text-right font-semibold">
                            ¥{{ number_format($order->total_amount) }}
                        </td>

                        <td class="p-4 text-center">

                            <span class="inline-block bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full">

                                {{ $order->status_label }}

                            </span>

                        </td>

                        <td class="p-4 text-center">

                            <a href="{{ route('mypage.orders.show', $order) }}"
                               class="text-blue-600 hover:underline">

                                詳細を見る

                            </a>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    @endif

</div>

@endsection
