@extends('shop.layout')

@section('content')

<div class="max-w-5xl mx-auto py-14 px-4">
    <h1 class="text-2xl font-bold mb-10 text-center">ショッピングカート</h1>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(empty($cart))
        <p class="text-center text-gray-500">カートに商品は入っていません。</p>
    @else

    <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-3">商品</th>
                    <th class="border p-3">価格</th>
                    <th class="border p-3">数量</th>
                    <th class="border p-3">小計</th>
                    <th class="border p-3">削除</th>
                </tr>
            </thead>
            <tbody>

            @php $total = 0; @endphp

            @foreach($cart as $key => $item)
                @php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                @endphp

                <tr data-key="{{ $key }}" data-price="{{ $item['price'] }}">
                    <td class="border p-3">
                        <div>{{ $item['name'] }}</div>
                        @if(!empty($item['member_name']))
                            <div class="text-xs text-gray-500">メンバー：{{ $item['member_name'] }}</div>
                        @endif
                    </td>

                    <td class="border p-3 text-right">
                        ¥{{ number_format($item['price']) }}
                    </td>

                    <td class="border p-3 text-center">
                        <input
                            type="number"
                            class="cart-qty border rounded px-2 py-1 w-20 text-center"
                            value="{{ $item['quantity'] }}"
                            min="1"
                        >
                    </td>

                    <td class="border p-3 text-right cart-subtotal">
                        ¥{{ number_format($subtotal) }}
                    </td>

                    {{-- ✅ ✅ ✅ 削除ボタン【強制表示・CSS完全無効化】 --}}
                    <td class="border p-3 text-center">
                        <form action="{{ route('cart.remove') }}" method="POST">
                            @csrf
                            <input type="hidden" name="key" value="{{ $key }}">

                            <button
                                type="submit"
                                onclick="return confirm('削除しますか？')"
                                style="
                                    background:#e11d48;
                                    color:#fff;
                                    padding:6px 12px;
                                    border-radius:6px;
                                    font-size:12px;
                                    border:none;
                                    cursor:pointer;
                                    opacity:1 !important;
                                    visibility:visible !important;
                                    display:inline-block !important;
                                "
                            >
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    <div class="text-right mt-6 text-lg font-bold" id="cart-total">
        合計：¥{{ number_format($total) }}
    </div>

    <div class="flex justify-end gap-4 mt-10">
        <a href="{{ route('store.index') }}" class="px-6 py-3 border rounded">
            買い物を続ける
        </a>
        <a href="{{ route('checkout.index') }}" class="px-6 py-3 bg-black text-white rounded">
            購入へ進む
        </a>
    </div>
</div>

<script>
document.querySelectorAll('.cart-qty').forEach(input => {
    input.addEventListener('change', function () {

        const row = this.closest('tr');
        const key = row.dataset.key;
        const price = Number(row.dataset.price);
        const qty = Number(this.value);

        const subtotal = price * qty;
        row.querySelector('.cart-subtotal').innerText = '¥' + subtotal.toLocaleString();

        let total = 0;
        document.querySelectorAll('.cart-subtotal').forEach(el => {
            total += Number(el.innerText.replace(/[¥,]/g, ''));
        });
        document.getElementById('cart-total').innerText = '合計：¥' + total.toLocaleString();

        fetch("{{ route('cart.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                key: key,
                quantity: qty
            })
        });
    });
});
</script>

@endsection
