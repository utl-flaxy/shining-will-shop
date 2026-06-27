<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
@if($isAdmin ?? false)
    <h2>🚨 新規注文が入りました</h2>

    <p><strong>注文番号：</strong>{{ $order->order_number }}</p>
    <p><strong>購入者：</strong>{{ $order->customer_name }}</p>
    <p><strong>メール：</strong>{{ $order->customer_email }}</p>
    <p><strong>合計金額：</strong>{{ number_format($order->total_amount) }} 円</p>

    <hr>
    <p>管理画面で対応してください。</p>

@else
    <h2>{{ $order->customer_name }} 様</h2>

    <p>この度は <strong>Shining Will Shop</strong> でご注文いただき、誠にありがとうございます。</p>

    <hr>

    <p><strong>■ ご注文番号：</strong>{{ $order->order_number }}</p>
    <p><strong>■ お支払い方法：</strong>{{ $order->payment_method }}</p>
    <p><strong>■ 合計金額：</strong>{{ number_format($order->total_amount) }} 円</p>

    <hr>

    <p>現在のご注文状況：<strong>{{ $order->status }}</strong></p>

    <p>発送準備が整い次第、追跡番号をお知らせいたします。</p>

    <br>

    <p>───</p>
    <p>Shining Will Shop</p>
@endif
</body>
</html>
