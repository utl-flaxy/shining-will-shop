<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>オーナーダッシュボード</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow mb-8">
        <div class="max-w-6xl mx-auto py-4 px-6 flex justify-between">
            <h1 class="text-xl font-bold">オーナーダッシュボード</h1>
            <form action="{{ route('owner.logout') }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    ログアウト
                </button>
            </form>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-500">商品数</h2>
                <p class="text-3xl font-bold">{{ $totalProducts }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-500">注文数</h2>
                <p class="text-3xl font-bold">{{ $totalOrders }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-gray-500">最近の注文</h2>
                <p class="text-3xl font-bold">{{ count($recentOrders) }}</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">最新 5件の注文</h2>

            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 border">注文番号</th>
                        <th class="p-3 border">購入者</th>
                        <th class="p-3 border">合計</th>
                        <th class="p-3 border">ステータス</th>
                        <th class="p-3 border">詳細</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($recentOrders as $order)
                    <tr>
                        <td class="p-3 border">{{ $order->order_number }}</td>
                        <td class="p-3 border">{{ $order->customer_name }}</td>
                        <td class="p-3 border">¥{{ number_format($order->total_amount) }}</td>
                        <td class="p-3 border">{{ $order->status }}</td>
                        <td class="p-3 border text-center">
                            <a href="{{ route('owner.orders.show', $order) }}"
                               class="px-3 py-1 bg-blue-500 text-white rounded">
                                開く
                            </a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
