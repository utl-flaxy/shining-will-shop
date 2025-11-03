<table class="min-w-full bg-white border border-gray-200 rounded-md">
    <thead>
        <tr class="bg-gray-100 text-gray-700 text-sm">
            <th class="px-3 py-2 text-left">商品名</th>
            <th class="px-3 py-2 text-left">個数</th>
            <th class="px-3 py-2 text-left">購入者名</th>
            <th class="px-3 py-2 text-left">購入日</th>
            <th class="px-3 py-2 text-left">金額</th>
            <th class="px-3 py-2 text-left">ステータス</th>
            <th class="px-3 py-2 text-left">操作</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sales as $sale)
            <tr class="border-t text-sm">
                <td class="px-3 py-2">{{ $sale['product_name'] }}</td>
                <td class="px-3 py-2">{{ $sale['quantity'] }}</td>
                <td class="px-3 py-2">{{ $sale['buyer'] }}</td>
                <td class="px-3 py-2">{{ $sale['created_at'] }}</td>
                <td class="px-3 py-2">{{ $sale['amount'] }} 円</td>
                <td class="px-3 py-2">{{ $sale['status'] }}</td>
                <td class="px-3 py-2">
                    @if ($sale['status'] === 'succeeded')
                        <form action="{{ route('admin.refund', $sale['id']) }}" method="POST" onsubmit="return confirm('本当に返金しますか？')">
                            @csrf
                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                                返金
                            </button>
                        </form>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-gray-500 py-4">売上データがありません</td>
            </tr>
        @endforelse
    </tbody>
</table>
