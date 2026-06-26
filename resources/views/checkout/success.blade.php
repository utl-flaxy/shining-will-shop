@extends('layouts.app')

@section('content')
<div class="container text-center p-10">
    <h2 class="text-2xl font-bold text-green-600 mb-4">✅ ご注文ありがとうございました！</h2>
    <a href="{{ route('store.index') }}" class="text-pink-500">ショップへ戻る</a>
</div>
@endsection
