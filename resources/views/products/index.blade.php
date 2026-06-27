@php
    // products/index.blade.php
    // 各商品カードの先頭画像 URL を安全に決めるユーティリティ
    // 対応する可能性:
    // - $product->images が Eloquent Collection の場合 (ProductImage モデル の url カラムを使用)
    // - $product->images が配列（以前の実装）で URL の配列が入っている場合
    // - $product->image が Filament 等で保持されている単一カラムの場合
@endphp

@foreach($products as $product)
  <div class="card product-card">
    @php
      $firstImageUrl = null;

      // 1) Eloquent relation が読み込まれている / Collection の場合
      if (isset($product->images) && is_object($product->images) && method_exists($product->images, 'first')) {
          $first = $product->images->first();
          if ($first) {
              $firstImageUrl = asset('storage/' . ($first->url ?? $first->path ?? $first->filename ?? ''));
          }
      }

      // 2) もし images が配列として渡されている場合（旧実装）
      if (!$firstImageUrl && !empty($product->images) && is_array($product->images) && count($product->images)) {
          $firstImageUrl = asset('storage/' . $product->images[0]);
      }

      // 3) Filament 等で product->image の単一カラムに保存しているケース
      if (!$firstImageUrl && !empty($product->image)) {
          // 既存テンプレ (owner views で使っている形式に合わせる)
          $firstImageUrl = asset('storage/products/' . $product->image);
      }

      // 4) フォールバック（no-image）
      if (!$firstImageUrl) {
          $firstImageUrl = asset('images/no-image.png');
      }
    @endphp

    <a href="{{ route('products.show', $product) }}" style="border:1px solid #eee;padding:8px;display:block;text-decoration:none;color:inherit;border-radius:8px;">
      <img src="{{ $firstImageUrl }}" class="card-img-top" alt="{{ $product->name }}" style="height:160px;object-fit:cover;">
      <div class="card-body">
        <h5 class="card-title">{{ $product->name }}</h5>
        <p class="card-text text-muted mb-2">{{ Str::limit($product->description, 80) }}</p>
        <p class="mb-2"><strong>{{ number_format($product->price) }}円</strong></p>
        <a href="{{ route('products.show', $product) }}" class="btn btn-primary btn-sm">詳細</a>
      </div>
    </a>
  </div>
@endforeach
