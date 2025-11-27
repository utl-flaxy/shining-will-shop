@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">💳 購入手続き</h1>

    @if (count($cart) === 0)
        <p>カートが空です。<a href="{{ url('/') }}" class="text-pink-500">商品一覧に戻る</a></p>
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
                @php $total = 0; @endphp
                @foreach ($cart as $item)
                    @php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; @endphp
                    <tr class="border-b">
                        <td class="p-3">{{ $item['name'] }}</td>
                        <td class="p-3">¥{{ number_format($item['price']) }}</td>
                        <td class="p-3">{{ $item['quantity'] }}</td>
                        <td class="p-3">¥{{ number_format($subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right text-lg font-semibold mb-6">
            合計金額：¥{{ number_format($total) }}
        </div>

        {{-- 購入フォーム --}}
        <form action="{{ route('checkout.start') }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded shadow">
            @csrf
            <div class="mb-4">
                <label class="block font-semibold mb-2">氏名</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">メールアドレス</label>
                <input type="email" name="email" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block font-semibold mb-2">住所</label>
                <textarea name="address" required class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <button type="submit"
                class="bg-pink-500 hover:bg-pink-600 text-white font-semibold px-6 py-2 rounded transition">
                注文を確定する
            </button>
        </form>
    @endif
</div>
@endsection
