<x-filament::page>

    {{-- =========================
        KPI
    ========================== --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">

        {{-- 売上合計 --}}
        <a
            href="{{ url('/admin/sales-page') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                💰 売上合計
            </div>

            <div class="mt-2 text-3xl font-bold">
                ¥{{ number_format($totalSales) }}
            </div>
        </a>

        {{-- 今日の売上 --}}
        <a
            href="{{ url('/admin/sales-page') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                📅 今日の売上
            </div>

            <div class="mt-2 text-3xl font-bold">
                ¥{{ number_format($todaySales) }}
            </div>
        </a>

        {{-- 今月の売上 --}}
        <a
            href="{{ url('/admin/sales-page') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                📈 今月の売上
            </div>

            <div class="mt-2 text-3xl font-bold">
                ¥{{ number_format($monthlySales) }}
            </div>
        </a>

        {{-- 注文数 --}}
        <a
            href="{{ url('/admin/orders') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                🛒 注文数
            </div>

            <div class="mt-2 text-3xl font-bold">
                {{ number_format($orderCount) }}
            </div>
        </a>

        {{-- 商品数 --}}
        <a
            href="{{ url('/admin/products') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                📦 商品数
            </div>

            <div class="mt-2 text-3xl font-bold">
                {{ number_format($productCount) }}
            </div>
        </a>

        {{-- 会員数 --}}
        <a
            href="{{ url('/admin/users') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                👤 会員数
            </div>

            <div class="mt-2 text-3xl font-bold">
                {{ number_format($userCount) }}
            </div>
        </a>

        {{-- カテゴリ数 --}}
        <a
            href="{{ url('/admin/categories') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                🏷 カテゴリ数
            </div>

            <div class="mt-2 text-3xl font-bold">
                {{ number_format($categoryCount) }}
            </div>
        </a>

        {{-- 在庫切れ商品 --}}
        <a
            href="{{ url('/admin/products') }}"
            class="block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="text-sm text-gray-500">
                ⚠ 在庫切れ商品
            </div>

            <div class="mt-2 text-3xl font-bold text-red-600">
                {{ number_format($soldOutCount) }}
            </div>
        </a>

    </div>

    {{-- =========================
        発送待ち
    ========================== --}}
    <a
        href="{{ url('/admin/orders') }}"
        class="mt-8 block rounded-xl bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-xl"
    >

        <div class="flex items-center justify-between">

            <h2 class="text-lg font-semibold">
                🚚 発送待ち注文
            </h2>

            <span
                class="rounded bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-700"
            >
                {{ $shippingWaitingCount }}件
            </span>

        </div>

    </a>

    {{-- =========================
        最新注文
    ========================== --}}
    <div class="mt-8 rounded-xl bg-white shadow">

        <div class="border-b p-6">

            <h2 class="text-lg font-semibold">
                🛒 最新注文
            </h2>

        </div>

        <table class="w-full">

            <thead class="bg-gray-50">

                <tr>

                    <th class="px-6 py-3 text-left">
                        注文番号
                    </th>

                    <th class="px-6 py-3 text-left">
                        購入者
                    </th>

                    <th class="px-6 py-3 text-right">
                        金額
                    </th>

                    <th class="px-6 py-3 text-center">
                        状態
                    </th>

                    <th class="px-6 py-3 text-right">
                        日時
                    </th>

                </tr>

            </thead>

            <tbody>

            @forelse($latestOrders as $order)

                <tr class="border-t hover:bg-gray-50 transition">

                    <td class="px-6 py-4 font-medium">

                        <a
                            href="{{ url('/admin/orders/' . $order->id) }}"
                            class="text-primary-600 hover:underline"
                        >
                            {{ $order->order_number }}
                        </a>

                    </td>

                    <td class="px-6 py-4">
                        {{ $order->customer_name }}
                    </td>

                    <td class="px-6 py-4 text-right font-semibold">
                        ¥{{ number_format($order->total_amount) }}
                    </td>

                    <td class="px-6 py-4 text-center">

                        @php
                            $color = match($order->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'preparing' => 'bg-blue-100 text-blue-700',
                                'shipped' => 'bg-indigo-100 text-indigo-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <span
                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $color }}"
                        >
                            {{ $order->status_label }}
                        </span>

                    </td>

                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                        {{ $order->created_at->format('Y/m/d H:i') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="5"
                        class="px-6 py-12 text-center text-gray-500"
                    >
                        注文データはありません。
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</x-filament::page>
