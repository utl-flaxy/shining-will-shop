@extends('owner.layouts.app')

@section('title', '商品詳細')
@section('page-title', '商品管理 - 商品詳細')

@section('content')
<div class="owner-card">

    <div class="owner-card-header">
        <h2 class="owner-card-title">商品詳細</h2>
        <a href="{{ route('owner.products.index') }}" class="owner-button">一覧へ戻る</a>
        <a href="{{ route('owner.products.edit', $product->id) }}" class="owner-button small secondary">編集</a>
    </div>

    <div class="owner-card-body">
        <div style="display:flex; gap:24px; align-items:flex-start;">
            {{-- 画像サイド --}}
            <div style="width:360px;">
                @php
                    // 画像の優先順:
                    // 1) $product->image (単一カラム) 2) $product->images->first()->path 3) fallback
                    $firstImageUrl = null;
                    if (!empty($product->image)) {
                        // index ビューが asset('storage/products/' . $product->image) を使っているようなのでそれに合わせる
                        $firstImageUrl = asset('storage/products/' . $product->image);
                    } elseif ($product->relationLoaded('images') && $product->images->isNotEmpty()) {
                        $img = $product->images->first();
                        // 画像モデルのカラム名が path/filename/url によって変わるので適宜修正してください
                        $firstImageUrl = asset('storage/products/' . ($img->path ?? $img->filename ?? $img->url ?? ''));
                    } else {
                        $firstImageUrl = asset('images/no-image.png');
                    }
                @endphp

                <img src="{{ $firstImageUrl }}"
                     alt="{{ $product->name }}"
                     style="width:100%; border:1px solid #eee; padding:8px; background:#fff;"
                     onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'">
            </div>

            {{-- 情報サイド --}}
            <div style="flex:1;">
                <h3>{{ $product->name }}</h3>
                <p>価格: ¥{{ number_format($product->price) }}</p>
                <p>在庫: {{ $product->stock }}</p>
                <p>カテゴリ: {{ optional($product->category)->name ?? '未設定' }}</p>

                <p>
                    公開状態:
                    @if ($product->is_active)
                        <span class="owner-badge success">公開</span>
                    @else
                        <span class="owner-badge muted">非公開</span>
                    @endif
                </p>

                <div style="margin-top:16px;">
                    <a href="{{ route('owner.products.edit', $product->id) }}" class="owner-button primary">編集する</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
