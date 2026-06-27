@extends('layouts.app')

@section('title', 'ご購入手続き')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-bold mb-6">ご購入内容の確認</h1>

    @if(empty($cart))
        <div class="text-center text-gray-500 py-10">
            カートに商品が入っていません。
        </div>
    @else

    <table class="w-full border text-sm mb-8">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2 text-left">商品名</th>
                <th class="border p-2 text-right">単価</th>
                <th class="border p-2 text-center">数量</th>
                <th class="border p-2 text-right">小計</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp

            @foreach($cart as $item)
                @php
                    $qty = $item['quantity'] ?? 1;
                    $price = $item['price'] ?? 0;
                    $subtotal = $price * $qty;
                    $total += $subtotal;
                @endphp
                <tr>
                    <td class="border p-2">{{ $item['name'] }}</td>
                    <td class="border p-2 text-right">¥{{ number_format($price) }}</td>
                    <td class="border p-2 text-center">{{ $qty }}</td>
                    <td class="border p-2 text-right">¥{{ number_format($subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="3" class="border p-3 text-right font-bold">合計金額</td>
                <td class="border p-3 text-right font-bold text-lg">
                    ¥{{ number_format($total) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <form method="POST" action="{{ route('checkout.start') }}" class="space-y-6">
        @csrf

        <select name="delivery_method" class="w-full border p-3">
            <option value="pickup">現地渡し</option>
            <option value="sagawa">佐川急便</option>
        </select>

        <textarea name="note_to_talent" class="w-full border p-3" placeholder="応援メッセージなど"></textarea>

        @if($total === 0)
            <button
                type="submit"
                class="checkout-submit-btn w-full mt-6 font-bold py-4 rounded"
            >
                ✅ 0円テスト注文を確定する
            </button>
        @else
            <button
                type="submit"
                class="checkout-submit-btn w-full mt-6 font-bold py-4 rounded"
                style="background-color:#16a34a;"
            >
                Squareで決済する
            </button>
        @endif

    </form>

    @endif

</div>
@endsection
