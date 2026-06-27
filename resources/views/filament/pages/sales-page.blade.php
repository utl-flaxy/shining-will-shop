<x-filament::page>
    <h2 class="text-lg font-bold mb-6">売上一覧（Square連携予定）</h2>

    @if (count($sales) === 0)
        <div class="p-6 bg-white rounded shadow text-gray-600">
            現在、売上データはありません。<br>
            Square連携後にここへ表示されます。
        </div>
    @else
        <table class="w-full bg-white rounded shadow overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">商品名</th>
                    <th class="p-3 text-left">購入者</th>
                    <th class="p-3 text-right">金額</th>
                    <th class="p-3 text-left">日付</th>
                    <th class="p-3 text-left">状態</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr class="border-t">
                        <td class="p-3">{{ $sale['id'] }}</td>
                        <td class="p-3">{{ $sale['product_name'] }}</td>
                        <td class="p-3">{{ $sale['buyer'] }}</td>
                        <td class="p-3 text-right">¥{{ $sale['amount'] }}</td>
                        <td class="p-3">{{ $sale['created_at'] }}</td>
                        <td class="p-3">{{ $sale['status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-filament::page>
