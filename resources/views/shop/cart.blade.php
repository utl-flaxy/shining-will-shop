@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">🛒 カート</h1>

    @if (count($cart) === 0)
        <p>カートは空です。</p>
    @else
        <table class="w-full mb-6 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="p-3 text-left">商品名</th>
                    <th class="p-3 text-left">価格</th>
                    <th class="p-3 text-left">数量</th>
                    <th class="p-3 text-left">小計</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $item)
                    <tr class="border-b">
                        <td class="p-3">{{ $item['name'] }}</td>
                        <td class="p-3">¥{{ number_format($item['price']) }}</td>
                        <td class="p-3">{{ $item['quantity'] }}</td>
                        <td class="p-3">¥{{ number_format($item['price'] * $item['quantity']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mb-6 text-lg">
            合計金額: <strong>¥{{ number_format($total) }}</strong>
        </div>

        <div class="text-right">
            <a href="{{ route('checkout.index') }}"
               class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded transition">
                購入手続きへ進む
            </a>
        </div>
    @endif
</div>
@endsection
