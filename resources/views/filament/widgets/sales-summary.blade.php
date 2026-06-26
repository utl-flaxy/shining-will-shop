<div class="filament-card p-4">
  <div class="flex items-center justify-between">
    <div>
      <div class="text-sm text-gray-500">本日の売上</div>
      <div class="text-2xl font-bold">¥{{ number_format($this->getData()['today'] ?? 0) }}</div>
    </div>

    <div class="text-right">
      <div class="text-sm text-gray-500">今月の売上</div>
      <div class="text-lg font-semibold text-amber-600">¥{{ number_format($this->getData()['month'] ?? 0) }}</div>
    </div>
  </div>
</div>
