@extends('owner.layouts.app')

@section('title', '商品一覧')
@section('page-title', '商品一覧')

@section('content')

<div class="owner-card">
    <div class="owner-card-header">
        <h2 class="owner-card-title">商品一覧</h2>

        <a href="{{ route('owner.products.create') }}" class="owner-button primary">
            ＋ 新規商品追加
        </a>
    </div>

    {{-- 検索フォーム --}}
    <form method="GET" action="{{ route('owner.products.index') }}" class="owner-search-form">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="商品名で検索" class="owner-search-input">
        <button class="owner-button secondary small">検索</button>
    </form>

    {{-- 商品一覧テーブル --}}
    <div class="owner-table-wrapper mt-3">
        <table class="owner-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>公開状態</th>
                    <th>更新日</th>
                    <th class="text-right">操作</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>

                        {{-- 画像 --}}
                        <td>
                            @if ($product->image)
                                <img src="{{ asset('storage/products/' . $product->image) }}"
                                     alt="product image"
                                     class="owner-thumb">
                            @else
                                <span class="text-muted">なし</span>
                            @endif
                        </td>

                        <td>{{ $product->name }}</td>
                        <td>¥{{ number_format($product->price) }}</td>
                        <td>{{ $product->stock }}</td>

                        <td>
                            <span class="owner-badge status-{{ $product->is_active ? 'active' : 'inactive' }}">
                                {{ $product->is_active ? '公開' : '非公開' }}
                            </span>
                        </td>

                        <td>{{ $product->updated_at->format('Y-m-d H:i') }}</td>

                        {{-- 操作 --}}
                        <td class="text-right">

                            <a href="{{ route('owner.products.edit', $product->id) }}"
                               class="owner-button small">
                                編集
                            </a>

                            <form action="{{ route('owner.products.destroy', $product->id) }}"
                                  method="POST"
                                  class="inline-form"
                                  onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')

                                <button class="owner-button danger small">
                                    削除
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">商品がありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ページネーション --}}
    <div class="owner-pagination">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
</div>

@endsection
