<x-filament::page>
    <div class="space-y-6">
        <h2 class="text-xl font-bold">売上一覧</h2>

        {{-- Excel出力フォーム --}}
        <form action="{{ route('admin.export-sales') }}" method="GET" class="bg-gray-50 p-4 rounded-md border flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-sm font-medium">開始日</label>
                <input type="date" name="start_date" class="border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm font-medium">終了日</label>
                <input type="date" name="end_date" class="border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm font-medium">商品名</label>
                <input type="text" name="product_name" placeholder="例: Tシャツ" class="border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm font-medium">購入者名</label>
                <input type="text" name="buyer_name" placeholder="例: 山田太郎" class="border rounded px-2 py-1">
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Excel出力
            </button>
        </form>

        {{-- 売上テーブル --}}
        @include('filament.pages.partials.sales-table', ['sales' => $sales])
    </div>
</x-filament::page>
