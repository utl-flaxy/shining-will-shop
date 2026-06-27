@component('mail::message')
{{ $order->customer_name }} 様

Shining Will Shop にてご注文いただいた件につきまして、
以下の内容で返金処理が完了いたしました。

@component('mail::panel')
注文番号：{{ $order->order_number }}
返金金額：{{ number_format($order->refunded_amount) }}円
返金日時：{{ optional($order->refunded_at)->format('Y-m-d H:i') }}
@endcomponent

■返金理由
{{ $order->refund_reason }}

※クレジットカード払いの場合、カード会社の締め日により、
返金の反映までお時間をいただく場合がございます。

ご不明な点がございましたら、お手数ですが本メールにご返信のうえお問い合わせください。

@endcomponent
