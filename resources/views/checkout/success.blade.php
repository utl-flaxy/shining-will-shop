@extends('layouts.app')

@section('title', '購入完了')

@section('content')
<div class="text-center">
  <h1>ご購入ありがとうございました</h1>
  <p>注文の処理が完了しました。確認メールが届きます。</p>
  <a class="btn btn-primary" href="{{ route('products.index') }}">トップへ戻る</a>
</div>
@endsection
