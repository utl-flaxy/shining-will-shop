@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>購入完了🎉</h1>
    <p>{{ $message ?? 'ご購入ありがとうございました！' }}</p>

    <a href="{{ route('products.index') }}" class="btn btn-success mt-3">商品一覧に戻る</a>
</div>
@endsection
