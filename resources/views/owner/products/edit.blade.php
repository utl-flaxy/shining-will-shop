@extends('owner.layouts.app')

@section('title', '商品編集')
@section('page-title', '商品編集')

@section('content')

<div class="owner-card">
    <form method="POST"
          action="{{ route('owner.products.update', $product->id) }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <!-- 商品名 -->
        <div class="owner-form-group">
            <label>商品名</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $product->name) }}"
                   required>
        </div>

        <!-- 価格 -->
        <div class="owner-form-group">
            <label>価格（円）</label>
            <input type="number"
                   name="price"
                   value="{{ old('price', $product->price) }}"
                   min="0"
                   required>
        </div>

        <!-- 在庫 -->
        <div class="owner-form-group">
            <label>在庫数</label>
            <input type="number"
                   name="stock"
                   value="{{ old('stock', $product->stock) }}"
                   min="0"
                   required>
        </div>

        <!-- 商品説明 -->
        <div class="owner-form-group">
            <label>商品説明</label>
            <textarea name="description" rows="4">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- 公開設定 -->
        <div class="owner-form-group">
            <label>公開設定</label>
            <select name="is_active" required>
                <option value="1" {{ $product->is_active ? 'selected' : '' }}>公開</option>
                <option value="0" {{ !$product->is_active ? 'selected' : '' }}>非公開</option>
            </select>
        </div>

        <!-- 現在の画像 -->
        <div class="owner-form-group">
            <label>現在の画像</label><br>

            @if($product->image)
                <img src="{{ asset('storage/products/' . $product->image) }}"
                     style="max-width: 200px; margin-bottom: 10px;">
            @else
                <p>画像なし</p>
            @endif
        </div>

        <!-- 新しい画像 -->
        <div class="owner-form-group">
            <label>画像を変更（任意）</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <!-- ボタン -->
        <div class="owner-form-actions">
            <button type="submit" class="owner-btn-primary">
                更新する
            </button>

            <a href="{{ route('owner.products.index') }}"
               class="owner-btn-secondary">
                戻る
            </a>
        </div>
    </form>
</div>

@endsection
