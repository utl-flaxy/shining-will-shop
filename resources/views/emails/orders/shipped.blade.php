@component('mail::message')
{{ $order->customer_name }} 様

Shining Will Shop にてご注文いただきありがとうございます。
以下のご注文商品の発送が完了いたしました。

@component('mail::panel')
注文番号：{{ $order->order_number }}
注文日時：{{ $order->created_at->format('Y-m-d H:i') }}
合計金額：{{ number_format($order->total_amount) }}円
配送方法：{{ $order->delivery_method_label }}
送り状番号：{{ $order->tracking_number ?? '―' }}
@endcomponent

{{-- タレントへのメッセージをそのまま記載するかは運用に合わせて調整 --}}
@if($order->note_to_talent)
■タレントへのメッセージ

{{ $order->note_to_talent }}

@endif

今後とも Shining Will の応援をよろしくお願いいたします。

@endcomponent
