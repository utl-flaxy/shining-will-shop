<div style="max-width: 600px; margin: 100px auto; padding: 40px; text-align: center;">
  <!-- Cancel Icon -->
  <div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center;">
    <svg style="width: 48px; height: 48px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
  </div>

  <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 16px; color: #111827;">
    お支払いがキャンセルされました
  </h1>
  
  <p style="font-size: 16px; color: #6b7280; margin-bottom: 32px; line-height: 1.6;">
    お支払い処理が中断されました。<br>
    カート内の商品は保存されていますので、再度お手続きいただけます。
  </p>

  <div style="display: flex; flex-direction: column; gap: 12px; max-width: 300px; margin: 0 auto;">
    <a 
      href="{{ route('cart.index') }}" 
      style="display: block; padding: 14px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;"
    >
      カートに戻る
    </a>
    
    <a 
      href="{{ route('store.index') }}" 
      style="display: block; padding: 14px 24px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;"
    >
      買い物を続ける
    </a>
  </div>

  <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
    <p style="font-size: 14px; color: #6b7280;">
      ご不明な点がございましたら、お気軽にお問い合わせください。
    </p>
  </div>
</div>
