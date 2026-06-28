<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>発送完了のお知らせ</title>
</head>
<body style="margin:0;padding:0;background:#f6f6f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr>
<td align="center">

<table width="640" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">

<tr>
<td style="background:#ec4899;padding:30px;text-align:center;">

<h1 style="margin:0;color:#ffffff;">
Shining Will Shop
</h1>

<p style="margin-top:10px;color:#ffffff;">
発送完了のお知らせ
</p>

</td>
</tr>

<tr>
<td style="padding:40px;">

<p>
{{ $order->customer_name }} 様
</p>

<p>
ご注文いただいた商品を発送いたしました。
</p>

<hr>

<h3>注文情報</h3>

<p>
注文番号：{{ $order->order_number }}
</p>

<p>
発送状況：{{ $order->status_label }}
</p>

@if($order->tracking_number)
<p>
追跡番号：{{ $order->tracking_number }}
</p>
@endif

<hr>

<h3>ご注文商品</h3>

<table width="100%" cellpadding="8" cellspacing="0">

<thead>

<tr style="background:#fafafa">

<th align="left">
商品
</th>

<th align="center">
数量
</th>

<th align="right">
金額
</th>

</tr>

</thead>

<tbody>

@foreach($order->items as $item)

<tr>

<td>
{{ $item->product_name }}
</td>

<td align="center">
{{ $item->quantity }}
</td>

<td align="right">
¥{{ number_format($item->price) }}
</td>

</tr>

@endforeach

</tbody>

</table>

<hr>

<p style="font-size:18px;font-weight:bold;text-align:right;">
合計
¥{{ number_format($order->total_amount) }}
</p>

<p style="margin-top:40px;">
商品到着まで今しばらくお待ちください。
</p>

<p>
今後とも Shining Will Shop をよろしくお願いいたします。
</p>

</td>
</tr>

<tr>

<td style="background:#fafafa;padding:20px;text-align:center;color:#888;font-size:13px;">

© {{ date('Y') }} Shining Will Shop

</td>

</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
