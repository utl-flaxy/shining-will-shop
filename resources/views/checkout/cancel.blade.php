@extends('layouts.app')

@section('title', '購入キャンセル')

@section('content')
<div class="text-center">
  <h1>購入がキャンセルされました</h1>
  <p>支払いを中止しました。必要に応じて再度カートから購入してください。</p>
  <a class="btn btn-primary" href="{{ route('cart.index') }}">カートを見る</a>
</div>
@endsection