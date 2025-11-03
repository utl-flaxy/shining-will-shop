@extends('layouts.app')

@section('content')
<div class="container">
    <h1>購入確認</h1>

    <table class="table">
        <thead>
            <tr>
                <th>商品名</th>
                <th>価格</th>
                <th>数量</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cart as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['price'] }}円</td>
                    <td>{{ $item['quantity'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>合計金額：{{ $total }}円</h3>

    <form action="{{ route('checkout.complete') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">購入を確定する</button>
    </form>
</div>
@endsection
