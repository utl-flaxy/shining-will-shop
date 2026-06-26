@extends('owner.layouts.app')

@section('title', '商品追加')
@section('page-title', '新規商品追加')

@section('content')

<div class="owner-card">

    <div class="owner-card-header">
        <h2 class="owner-card-title">新規商品追加</h2>

        <a href="{{ route('owner.products.index') }}" class="owner-button secondary">
            ← 商品一覧へ戻る
        </a>
    </div>

    <form action="{{ route('owner.products.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="owner-form">

        @csrf

        {{-- 商品名 --}}
        <div class="owner-form-group">
            <label class="owner-form-label">商品名 <span class="required">*</span></label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="owner-input"
                   required>
        </div>

        {{-- 商品説明 --}}
        <div class="owner-form-group">
            <label class="owner-form-label">商品説明</label>
            <textarea name="description"
                      rows="4"
                      class="owner-input">{{ old('description') }}</textarea>
        </div>

        {{-- 価格 --}}
        <div class="owner-form-group">
            <label class="owner-form-label">価格 <span class="required">*</span></label>
            <input type="number"
                   name="price"
                   value="{{ old('price') }}"
                   min="0"
                   class="owner-input"
                   required>
        </div>

        {{-- 在庫数 --}}
        <div class="owner-form-group">
            <label class="owner-form-label">在庫数</label>
            <input type="number"
                   name="stock"
                   value="{{ old('stock', 0) }}"
                   min="0"
                   class="owner-input">
        </div>

        {{-- カテゴリ --}}
        <div class="owner-form-group">
            <label class="owner-form-label">カテゴリ</label>
            <select name="category_id" class="owner-input">
                <option value="">未選択</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 画像アップロード --}}
        <div class="owner-form-group">
            <label class="owner-form-label">商品画像</label>
            <input type="file"
                   name="image"
                   accept="image/*"
                   class="owner-input">
        </div>

        {{-- 公開/非公開 --}}
        <div class="owner-form-group">
            <label class="owner-form-label">公開状態</label>
            <select name="is_active" class="owner-input">
                <option value="1" {{ old('is_active') == "1" ? "selected" : "" }}>公開</option>
                <option value="0" {{ old('is_active') == "0" ? "selected" : "" }}>非公開</option>
            </select>
        </div>

        {{-- 登録ボタン --}}
        <div class="owner-form-submit">
            <button class="owner-button primary large" type="submit">
                登録する
            </button>
        </div>

    </form>
</div>

@endsection
