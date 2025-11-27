{{-- resources/views/owner/products/delete.blade.php --}}
@extends('owner.layouts.app')

@section('title', '商品削除')
@section('page-title', '商品削除確認')

@section('content')
    <div class="owner-card">

        <div class="owner-alert error" style="margin-bottom:20px;">
            この商品を本当に削除しますか？
            <br>削除すると元に戻すことはできません。
        </div>

        {{-- 商品情報プレビュー --}}
        <div class="owner-product-preview">
            <h2 class="owner-label" style="font-size:18px; margin-bottom:5px;">
                {{ $product->name }}
            </h2>

            @if($product->image)
                <img src="{{ asset('storage/products/' . $product->image) }}"
                     style="width:200px; border-radius:6px; border:1px solid #ccc; margin-bottom:15px;">
            @endif

            <p><strong>価格：</strong>{{ number_format($product->price) }} 円</p>
            <p><strong>在庫：</strong>{{ $product->stock }} 個</p>
            <p><strong>公開ステータス：</strong>
                @if($product->is_active)
                    <span style="color:green;">公開</span>
                @else
                    <span style="color:red;">非公開</span>
                @endif
            </p>
        </div>

        {{-- 削除ボタン --}}
        <form action="{{ route('owner.products.destroy', $product->id) }}"
              method="POST"
              class="owner-form"
              style="margin-top:25px;">
            @csrf
            @method('DELETE')

            <div class="owner-form-actions">
                <a href="{{ route('owner.products.index') }}" class="owner-button-gray">
                    キャンセル
                </a>

                <button type="submit" class="owner-button-danger">
                    削除する
                </button>
            </div>
        </form>
    </div>
@endsection
