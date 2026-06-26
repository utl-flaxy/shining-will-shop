<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>{{ $order->customer_name }} 様</h2>

    <p>Shining Will Shop をご利用いただき誠にありがとうございます。</p>

    <p>ご注文の商品を <strong>本日発送いたしました。</strong></p>

    <hr>

    <p><strong>■ ご注文番号：</strong>{{ $order->order_number }}</p>
    <p><strong>■ 配送方法：</strong>{{ $order->delivery_method }}</p>
    <p><strong>■ 追跡番号：</strong>{{ $order->tracking_number ?? '未登録' }}</p>

    <hr>

    <p>お届けまで今しばらくお待ちください。</p>

    <br>

    <p>───</p>
    <p>Shining Will Shop</p>
</body>
</html>
