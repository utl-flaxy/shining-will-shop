{{-- resources/views/owner/products/edit.blade.php --}}
@extends('owner.layouts.app')

@section('title', '商品編集')
@section('page-title', '商品編集')

@section('content')
    <div class="owner-card">

        <form action="{{ route('owner.products.update', $product->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="owner-form">
            @csrf
            @method('PUT')

            {{-- 商品名 --}}
            <div class="owner-form-group">
                <label class="owner-label">商品名 <span class="required">*</span></label>
                <input type="text" name="name" class="owner-input"
                       value="{{ old('name', $product->name) }}" required>
            </div>

            {{-- 価格 --}}
            <div class="owner-form-group">
                <label class="owner-label">販売価格（円） <span class="required">*</span></label>
                <input type="number" name="price" class="owner-input" min="0"
                       value="{{ old('price', $product->price) }}" required>
            </div>

            {{-- 在庫 --}}
            <div class="owner-form-group">
                <label class="owner-label">在庫数 <span class="required">*</span></label>
                <input type="number" name="stock" class="owner-input" min="0"
                       value="{{ old('stock', $product->stock) }}" required>
            </div>

            {{-- 説明 --}}
            <div class="owner-form-group">
                <label class="owner-label">説明文</label>
                <textarea name="description" class="owner-textarea" rows="5">{{ old('description', $product->description) }}</textarea>
            </div>

            {{-- 公開ステータス --}}
            <div class="owner-form-group">
                <label class="owner-label">公開ステータス</label>
                <select name="is_active" class="owner-select">
                    <option value="1" {{ $product->is_active ? 'selected' : '' }}>公開</option>
                    <option value="0" {{ !$product->is_active ? 'selected' : '' }}>非公開</option>
                </select>
            </div>

            {{-- 画像 --}}
            <div class="owner-form-group">
                <label class="owner-label">商品画像（任意）</label>
                <input type="file" name="image" class="owner-input" accept="image/*" id="imageInput">

                {{-- 現在の画像 --}}
                @if ($product->image)
                    <div style="margin-top:10px;">
                        <p class="owner-label">現在の画像</p>
                        <img src="{{ asset('storage/products/' . $product->image) }}"
                             style="width:200px; border-radius:6px; border:1px solid #ccc;">
                    </div>
                @endif

                {{-- 新しい画像プレビュー --}}
                <div id="previewContainer" style="display:none; margin-top:10px;">
                    <p class="owner-label">新しいプレビュー</p>
                    <img id="imagePreview" src=""
                         style="width:220px; border-radius:6px; border:1px solid #ccc;">
                </div>
            </div>

            {{-- ボタン --}}
            <div class="owner-form-actions">
                <a href="{{ route('owner.products.index') }}" class="owner-button-gray">戻る</a>
                <button type="submit" class="owner-button-primary">更新する</button>
            </div>
        </form>
    </div>

    {{-- 画像プレビューJS --}}
    <script>
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const box = document.getElementById('previewContainer');

        input.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                box.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    </script>
@endsection
