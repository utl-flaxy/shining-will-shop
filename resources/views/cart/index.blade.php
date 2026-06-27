@extends('layouts.app')

@section('title', 'カート | Shining Will')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-semibold mb-6">ショッピングカート</h1>

    {{-- ✅ 成功メッセージ --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(empty($cart))
        <p class="text-gray-500">カートに商品が入っていません。</p>
    @else
        <table class="w-full border mb-6 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">商品</th>
                    <th class="p-2 text-center">価格</th>
                    <th class="p-2 text-center">数量</th>
                    <th class="p-2 text-center">小計</th>
                    <th class="p-2 text-center">削除</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp

                @foreach($cart as $key => $item)
                    @php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    @endphp
                    <tr class="border-t" data-key="{{ $key }}" data-price="{{ $item['price'] }}">
                        <td class="p-2">
                            {{ $item['name'] }}
                            @if(!empty($item['member_name']))
                                <div class="text-xs text-gray-500">メンバー：{{ $item['member_name'] }}</div>
                            @endif
                        </td>

                        <td class="p-2 text-center">
                            ¥{{ number_format($item['price']) }}
                        </td>

                        {{-- ✅ 数量（サイズ最適化） --}}
                        <td class="p-2 text-center">
                            <input
                                type="number"
                                class="cart-qty border w-20 h-8 text-center rounded"
                                value="{{ $item['quantity'] }}"
                                min="1"
                            >
                        </td>

                        <td class="p-2 text-center cart-subtotal">
                            ¥{{ number_format($subtotal) }}
                        </td>

                        {{-- ✅ 削除 --}}
                        <td class="p-2 text-center">
                            <form method="POST" action="{{ route('cart.remove') }}"
                                  onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                    削除
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ✅ 合計 --}}
        <div class="text-right text-xl font-semibold mb-6" id="cart-total">
            合計：¥{{ number_format($total) }}
        </div>

        {{-- ✅ 操作 --}}
        <div class="flex justify-end gap-4">
            <a href="{{ route('store.index') }}" class="border px-6 py-3">
                買い物を続ける
            </a>
            <a href="{{ route('checkout.index') }}" class="bg-black text-white px-6 py-3">
                購入へ進む
            </a>
        </div>
    @endif

</div>

{{-- ✅ 数量変更 Ajax --}}
<script>
document.querySelectorAll('.cart-qty').forEach(input => {
    input.addEventListener('change', function () {

        const row = this.closest('tr');
        const key = row.dataset.key;
        const price = Number(row.dataset.price);
        const qty = Number(this.value);

        if (qty < 1) {
            this.value = 1;
            return;
        }

        const subtotal = price * qty;
        row.querySelector('.cart-subtotal').innerText = '¥' + subtotal.toLocaleString();

        let total = 0;
        document.querySelectorAll('.cart-subtotal').forEach(el => {
            total += Number(el.innerText.replace(/[¥,]/g, ''));
        });

        document.getElementById('cart-total').innerText =
            '合計：¥' + total.toLocaleString();

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
