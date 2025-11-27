@extends('layouts.app')

@section('content')
<div class="text-center py-10">
    <h1 class="text-3xl font-bold text-pink-500 mb-4">ご購入ありがとうございました！</h1>
    <p>お支払いが正常に完了しました。</p>
    <a href="{{ route('store.index') }}" class="mt-6 inline-block bg-pink-500 text-white px-6 py-3 rounded-md">
        ショップに戻る
    </a>
</div>
@endsection
