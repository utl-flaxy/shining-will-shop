@extends('layouts.admin')

@section('title', '商品登録')

@section('content')
<div class="admin-card">
  <h2 class="card-title">商品登録</h2>
  <p class="card-desc">商品登録を行うと、あなたのショップに商品が並びます。</p>

  <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-section">
      <label>商品名 <span class="required">必須</span></label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="form-section">
      <label>カテゴリー <span class="required">必須</span></label>
      <select name="category_id" class="form-control" required>
        <option value="">選択してください</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-section">
      <label>販売価格</label>
      <div class="price-field">
        <input type="number" name="price" class="form-control" min="0" required> <span>円</span>
      </div>
    </div>

    <div class="form-section">
      <label>商品画像</label>
      <input type="file" name="image" class="form-control">
    </div>

    <div class="form-section">
      <label>商品説明</label>
      <textarea name="description" class="form-control" rows="4"></textarea>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">登録する</button>
      <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">戻る</a>
    </div>
  </form>
</div>
@endsection
