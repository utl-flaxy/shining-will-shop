<div style="max-width: 800px; margin: 60px auto; padding: 40px; text-align: center;">
  <!-- Success Icon -->
  <div style="width: 80px; height: 80px; background: #d1fae5; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;">
    <svg style="width: 48px; height: 48px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
  </div>

  <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 16px; color: #059669;">
    ご購入ありがとうございました
  </h1>
  
  <p style="font-size: 18px; color: #6b7280; margin-bottom: 32px;">
    ご注文を承りました。確認メールをお送りしましたので、ご確認ください。
  </p>

  @if(isset($order) && $order)
    <div style="background: #f9fafb; padding: 24px; border-radius: 8px; margin-bottom: 32px;">
      <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">注文番号</div>
      <div style="font-size: 24px; font-weight: 700; color: #111827; font-family: monospace;">
        #{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}
      </div>
      
      @if($order->total_amount)
        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
          <div style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">お支払い金額</div>
          <div style="font-size: 28px; font-weight: 700; color: #3b82f6;">
            ¥{{ number_format($order->total_amount) }}
          </div>
        </div>
      @endif

      @if($order->items && $order->items->count() > 0)
        <div style="margin-top: 24px; text-align: left;">
          <div style="font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 12px;">ご注文内容</div>
          @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
              <div>
                <div style="font-weight: 500;">{{ $item->product_name }}</div>
                <div style="font-size: 14px; color: #6b7280;">数量: {{ $item->quantity }}</div>
              </div>
              <div style="font-weight: 600;">
                ¥{{ number_format($item->price * $item->quantity) }}
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif

  <div style="display: flex; gap: 16px; justify-content: center;">
    <a 
      href="{{ route('store.index') }}" 
      style="display: inline-block; padding: 14px 32px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;"
    >
      ショッピングを続ける
    </a>
  </div>

  <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
      商品の発送までに通常2〜3営業日かかります。<br>
      ご不明な点がございましたら、お気軽にお問い合わせください。
    </p>
  </div>
</div>
