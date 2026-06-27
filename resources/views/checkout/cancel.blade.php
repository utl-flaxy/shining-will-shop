@extends('layouts.app')

@section('content')
<div class="container text-center p-10">
    <h2 class="text-2xl font-bold text-red-600 mb-4">❌ お支払いがキャンセルされました</h2>
    <a href="{{ route('cart.index') }}" class="text-pink-500">カートへ戻る</a>
</div>
@endsection
