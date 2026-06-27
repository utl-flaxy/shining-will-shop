@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-2xl">

    <h1 class="text-2xl font-bold mb-6">💳 購入手続き</h1>

    @if (count($cart) === 0)
        <p>カートが空です。</p>
        <a href="{{ url('/') }}">商品一覧へ戻る</a>
    @else

        <table class="w-full mb-6 border">
            <thead>
                <tr>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($cart as $item)
                    @php
                        $subtotal = $item['price'] * $item['qty'];
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>¥{{ number_format($item['price']) }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>¥{{ number_format($subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right font-bold mb-6">
            合計金額：¥{{ number_format($total) }}
        </div>

        <form id="payment-form" action="{{ route('checkout.start') }}" method="POST">
            @csrf

            <label>氏名</label>
            <input type="text" name="name" required class="w-full mb-2 border">

            <label>メール</label>
            <input type="email" name="email" required class="w-full mb-2 border">

            <label>住所</label>
            <textarea name="address" required class="w-full mb-4 border"></textarea>

            <!-- ✅ SquareカードUI -->
            <div id="card-container" class="mb-4"></div>

            <input type="hidden" name="nonce" id="card-nonce">

            <button id="pay-button" type="button"
                class="bg-pink-500 text-white px-6 py-2 rounded">
                決済して注文する
            </button>
        </form>

    @endif
</div>

<!-- ✅ Square Web Payments SDK（HTTPS必須） -->
<script src="https://sandbox.web.squarecdn.com/v1/square.js"></script>

<script>
document.addEventListener('DOMContentLoaded', async () => {

    const appId = "{{ config('services.square.application_id') }}";
    const locationId = "{{ config('services.square.location_id') }}";

    const payments = Square.payments(appId, locationId);
    const card = await payments.card();
    await card.attach('#card-container');

    document.getElementById('pay-button').addEventListener('click', async () => {
        const result = await card.tokenize();

        if (result.status === 'OK') {
            document.getElementById('card-nonce').value = result.token;
            document.getElementById('payment-form').submit();
        } else {
            alert('カード情報が正しくありません');
        }
    });

});
</script>
@endsection
