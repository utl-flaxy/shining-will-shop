<x-filament::page>
    <div class="p-4">
        <table class="min-w-full border border-gray-300 rounded-lg">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Payment ID</th>
                    <th class="px-4 py-2 text-left">金額 (円)</th>
                    <th class="px-4 py-2 text-left">ステータス</th>
                    <th class="px-4 py-2 text-left">日時</th>
                    <th class="px-4 py-2 text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->sales as $sale)
                    <tr class="border-t border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $sale->id }}</td>
                        <td class="px-4 py-2">{{ number_format($sale->amount) }}</td>
                        <td class="px-4 py-2">{{ $sale->status }}</td>
                        <td class="px-4 py-2">{{ $sale->created }}</td>
                        <td class="px-4 py-2 text-center">
                            <form method="POST" action="{{ route('refund', ['id' => $sale->id]) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                    返金
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::page>
