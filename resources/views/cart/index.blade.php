@extends('layouts.app')

@section('title', 'カート | Shining Will')

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-semibold mb-6">
        ショッピングカート
    </h1>

    {{-- 成功メッセージ --}}
    @if(session('success'))
        <div class="mb-4 rounded border border-green-300 bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- エラーメッセージ --}}
    @if(session('error'))
        <div class="mb-4 rounded border border-red-300 bg-red-100 px-4 py-3 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if(empty($cart))

        <div class="rounded border bg-white p-10 text-center text-gray-500">
            カートに商品が入っていません。
        </div>

    @else

        @php
            $total = 0;
        @endphp

        <table class="w-full border text-sm mb-6">

            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">
                        商品
                    </th>

                    <th class="p-3 text-center">
                        価格
                    </th>

                    <th class="p-3 text-center">
                        数量
                    </th>

                    <th class="p-3 text-center">
                        小計
                    </th>

                    <th class="p-3 text-center">
                        削除
                    </th>
                </tr>
            </thead>

            <tbody>

            @foreach($cart as $key => $item)

                @php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                @endphp

                <tr
                    class="border-t"
                    data-key="{{ $key }}"
                    data-price="{{ $item['price'] }}"
                >

                    <td class="p-3">

                        <div class="font-medium">
                            {{ $item['name'] }}
                        </div>

                        @if(!empty($item['member_name']))
                            <div class="text-xs text-gray-500 mt-1">
                                メンバー：{{ $item['member_name'] }}
                            </div>
                        @endif

                    </td>

                    <td class="text-center p-3">
                        ¥{{ number_format($item['price']) }}
                    </td>

                    <td class="text-center p-3">

                        <input
                            type="number"
                            class="cart-qty w-20 rounded border text-center"
                            value="{{ $item['quantity'] }}"
                            min="1"
                        >

                    </td>

                    <td class="text-center p-3 cart-subtotal">
                        ¥{{ number_format($subtotal) }}
                    </td>

                    <td class="text-center p-3">

                        <form
                            method="POST"
                            action="{{ route('cart.remove') }}"
                            onsubmit="return confirm('本当に削除しますか？');"
                        >

                            @csrf

                            <input
                                type="hidden"
                                name="key"
                                value="{{ $key }}"
                            >

                            <button
                                class="rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600 transition"
                            >
                                削除
                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

        <div
            id="cart-total"
            class="mb-8 text-right text-2xl font-semibold"
        >
            合計：¥{{ number_format($total) }}
        </div>

        <div class="flex justify-end gap-4">

            <a
                href="{{ route('store.index') }}"
                class="border border-gray-300 px-6 py-3 hover:bg-gray-100 transition"
            >
                買い物を続ける
            </a>

            <a
                href="{{ route('checkout.index') }}"
                class="bg-black px-6 py-3 text-white hover:bg-gray-800 transition"
            >
                購入へ進む
            </a>

        </div>

    @endif

</div>
<script>

document.querySelectorAll('.cart-qty').forEach(input => {

    input.addEventListener('change', async function () {

        const row = this.closest('tr');

        const key = row.dataset.key;

        let qty = Number(this.value);

        if (qty < 1) {
            qty = 1;
            this.value = 1;
        }

        this.disabled = true;

        try {

            const response = await fetch(
                "{{ route('cart.update') }}",
                {
                    method: "POST",

                    credentials: "same-origin",

                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },

                    body: JSON.stringify({
                        key: key,
                        quantity: qty
                    })
                }
            );

            const data = await response.json();

            if (!response.ok) {

                alert(data.message ?? "数量を更新できませんでした。");

                location.reload();

                return;
            }

            if (!data.success) {

                alert(data.message ?? "数量を更新できませんでした。");

                location.reload();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | サーバー側で補正された数量を反映
            |--------------------------------------------------------------------------
            */

            this.value = data.quantity;

            /*
            |--------------------------------------------------------------------------
            | 小計更新
            |--------------------------------------------------------------------------
            */

            row.querySelector(".cart-subtotal").innerText =
                "¥" + Number(data.subtotal).toLocaleString();

            /*
            |--------------------------------------------------------------------------
            | 合計再計算
            |--------------------------------------------------------------------------
            */

            let total = 0;

            document.querySelectorAll(".cart-subtotal").forEach(el => {

                total += Number(
                    el.innerText.replace(/[¥,]/g, "")
                );

            });

            document.getElementById("cart-total").innerText =
                "合計：¥" + total.toLocaleString();

        } catch (error) {

            console.error(error);

            alert("通信エラーが発生しました。");

            location.reload();

        } finally {

            this.disabled = false;

        }

    });

});

</script>

@endsection
