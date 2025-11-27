@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <h1 class="text-3xl font-bold text-pink-500 mb-8">お支払い内容の確認</h1>

    <table class="w-full bg-white shadow rounded-lg mb-6">
        <thead>
            <tr class="bg-pink-50 text-pink-600">
                <th class="py-3 px-4 text-left">商品名</th>
                <th class="py-3 px-4 text-center">数量</th>
                <th class="py-3 px-4 text-right">価格</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart as $item)
                <tr class="border-t">
                    <td class="py-3 px-4">{{ $item['name'] }}</td>
                    <td class="py-3 px-4 text-center">{{ $item['quantity'] }}</td>
                    <td class="py-3 px-4 text-right">¥{{ number_format($item['price'] * $item['quantity']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ✅ 合計金額表示 --}}
    <div class="text-right text-xl font-semibold mb-6">
        合計：<span class="text-pink-500">¥{{ number_format($total) }}</span>
    </div>

    <form action="{{ route('checkout.start') }}" method="POST">
        @csrf
        <button type="submit"
            class="w-full bg-pink-500 hover:bg-pink-600 text-white py-3 rounded-md font-bold transition">
            Stripeで支払う
        </button>
    </form>
</div>
@endsection
