<x-filament::page>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <div class="rounded-xl bg-white shadow p-6">
            <p class="text-gray-500 text-sm">売上合計</p>

            <p class="text-3xl font-bold text-pink-600 mt-2">
                ¥{{ number_format($totalSales) }}
            </p>
        </div>

        <div class="rounded-xl bg-white shadow p-6">
            <p class="text-gray-500 text-sm">注文数</p>

            <p class="text-3xl font-bold mt-2">
                {{ $totalOrders }}件
            </p>
        </div>

        <div class="rounded-xl bg-white shadow p-6">
            <p class="text-gray-500 text-sm">今日の売上</p>

            <p class="text-3xl font-bold text-green-600 mt-2">
                ¥{{ number_format($todaySales) }}
            </p>
        </div>

        <div class="rounded-xl bg-white shadow p-6">
            <p class="text-gray-500 text-sm">今月の売上</p>

            <p class="text-3xl font-bold text-blue-600 mt-2">
                ¥{{ number_format($monthlySales) }}
            </p>
        </div>

    </div>

    <div class="rounded-xl bg-white shadow overflow-hidden">

        <div class="px-6 py-4 border-b">

            <h2 class="text-lg font-bold">
                最近の注文
            </h2>

        </div>

        <table class="w-full">

            <thead class="bg-gray-100">

            <tr>

                <th class="p-4 text-left">
                    注文番号
                </th>

                <th class="p-4 text-left">
                    購入者
                </th>

                <th class="p-4 text-right">
                    金額
                </th>

                <th class="p-4 text-left">
                    ステータス
                </th>

                <th class="p-4 text-left">
                    注文日時
                </th>

            </tr>

            </thead>

            <tbody>

            @forelse($recentOrders as $order)

                <tr class="border-t">

                    <td class="p-4">
                        {{ $order->order_number }}
                    </td>

                    <td class="p-4">
                        {{ $order->customer_name }}
                    </td>

                    <td class="p-4 text-right">
                        ¥{{ number_format($order->total_amount) }}
                    </td>

                    <td class="p-4">
                        {{ $order->status_label }}
                    </td>

                    <td class="p-4">
                        {{ $order->created_at->format('Y/m/d H:i') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" class="p-6 text-center text-gray-500">

                        注文データはありません。

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</x-filament::page>
