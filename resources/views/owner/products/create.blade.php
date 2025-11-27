{{-- resources/views/owner/products/create.blade.php --}}
@extends('owner.layouts.app')

@section('title', '商品追加')
@section('page-title', '商品追加')

@section('content')
    <div class="owner-card">

        <form action="{{ route('owner.products.store') }}" method="POST" enctype="multipart/form-data" class="owner-form">
            @csrf

            {{-- 商品名 --}}
            <div class="owner-form-group">
                <label class="owner-label">商品名 <span class="required">*</span></label>
                <input type="text" name="name" class="owner-input" required>
            </div>

            {{-- 金額 --}}
            <div class="owner-form-group">
                <label class="owner-label">販売価格（円） <span class="required">*</span></label>
                <input type="number" name="price" class="owner-input" min="0" required>
            </div>

            {{-- 在庫数 --}}
            <div class="owner-form-group">
                <label class="owner-label">在庫数 <span class="required">*</span></label>
                <input type="number" name="stock" class="owner-input" min="0" required>
            </div>

            {{-- 説明 --}}
            <div class="owner-form-group">
                <label class="owner-label">説明文</label>
                <textarea name="description" class="owner-textarea" rows="5"></textarea>
            </div>

            {{-- 公開ステータス --}}
            <div class="owner-form-group">
                <label class="owner-label">公開ステータス</label>
                <select name="is_active" class="owner-select">
                    <option value="1">公開</option>
                    <option value="0">非公開</option>
                </select>
            </div>

            {{-- 画像アップロード --}}
            <div class="owner-form-group">
                <label class="owner-label">商品画像（任意）</label>
                <input type="file" name="image" class="owner-input" accept="image/*" id="imageInput">

                {{-- プレビュー --}}
                <div id="previewContainer" style="display:none; margin-top:10px;">
                    <p class="owner-label">プレビュー</p>
                    <img id="imagePreview" src="" style="width:250px; border-radius:6px; border:1px solid #ccc;">
                </div>
            </div>

            <div class="owner-form-actions">
                <a href="{{ route('owner.products.index') }}" class="owner-button-gray">戻る</a>
                <button type="submit" class="owner-button-primary">保存する</button>
            </div>
        </form>
    </div>

    {{-- プレビューJS --}}
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
