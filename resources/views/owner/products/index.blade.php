@extends('owner.layouts.app')

@section('title', '商品一覧')
@section('page-title', '商品管理')

@section('content')
    <div class="owner-card">

        {{-- ヘッダー行 --}}
        <div class="owner-card-header">
            <h2 class="owner-card-title">商品一覧</h2>

            <a href="{{ route('owner.products.create') }}" class="owner-button primary">
                ＋ 新規商品追加
            </a>
        </div>

        {{-- 商品がまだない場合 --}}
        @if ($products->isEmpty())
            <p class="owner-empty">
                まだ商品が登録されていません。<br>
                「＋ 新規商品追加」から商品を登録してください。
            </p>
        @else
            <table class="owner-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">画像</th>
                        <th>商品名</th>
                        <th style="width: 140px;">カテゴリ</th>
                        <th style="width: 120px;">価格</th>
                        <th style="width: 80px;">在庫</th>
                        <th style="width: 110px;">公開状態</th>
                        <th style="width: 120px;">更新日</th>
                        <th style="width: 180px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            {{-- サムネイル --}}
                            <td class="owner-table-thumb">
                                @if ($product->image)
                                    <img src="{{ asset('storage/products/' . $product->image) }}"
                                         alt="{{ $product->name }}">
                                @else
                                    <span class="owner-badge muted">NO IMAGE</span>
                                @endif
                            </td>

                            {{-- 商品名 --}}
                            <td>
                                <div class="owner-table-title">{{ $product->name }}</div>
                            </td>

                            {{-- カテゴリ --}}
                            <td>
                                {{ optional($product->category)->name ?? '未設定' }}
                            </td>

                            {{-- 価格 --}}
                            <td>
                                ¥{{ number_format($product->price) }}
                            </td>

                            {{-- 在庫 --}}
                            <td>
                                {{ $product->stock }}
                            </td>

                            {{-- 公開状態 --}}
                            <td>
                                @if ($product->is_active)
                                    <span class="owner-badge success">公開</span>
                                @else
                                    <span class="owner-badge muted">非公開</span>
                                @endif
                            </td>

                            {{-- 更新日 --}}
                            <td>
                                {{ optional($product->updated_at)->format('Y-m-d') }}
                            </td>

                            {{-- 操作ボタン --}}
                            <td class="owner-table-actions">
                                <a href="{{ route('owner.products.edit', $product->id) }}"
                                   class="owner-button small secondary">
                                    編集
                                </a>

                                <form action="{{ route('owner.products.destroy', $product->id) }}"
                                      method="POST"
                                      class="owner-inline-form"
                                      onsubmit="return confirm('商品「{{ $product->name }}」を削除してよろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="owner-button small danger">
                                        削除
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
@endsection
