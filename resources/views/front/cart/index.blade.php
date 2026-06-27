<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
  <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 24px;">ショッピングカート</h1>

  @if(session('ok'))
    <div style="padding: 12px 16px; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 20px;">
      {{ session('ok') }}
    </div>
  @endif

  @if(empty($items))
    <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 8px;">
      <div style="font-size: 64px; margin-bottom: 16px;">🛒</div>
      <p style="font-size: 18px; color: #6b7280; margin-bottom: 24px;">カートは空です</p>
      <a 
        href="{{ route('store.index') }}" 
        style="display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;"
      >
        商品を探す
      </a>
    </div>
  @else
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
      <!-- Cart Items -->
      <div>
        <form method="post" action="{{ route('cart.update') }}">
          @csrf
          
          @foreach($items as $line)
            <div style="display: grid; grid-template-columns: 100px 1fr auto; gap: 16px; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px;">
              <!-- Product Image -->
              <div>
                @php $thumb = $line['images'][0] ?? null; @endphp
                @if($thumb)
                  <img 
                    src="{{ asset('storage/'.$thumb) }}" 
                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;"
                    alt="{{ $line['product']->name }}"
                  >
                @else
                  <div style="width: 100px; height: 100px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px;">
                    No Image
                  </div>
                @endif
              </div>

              <!-- Product Info -->
              <div>
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 4px;">
                  <a href="{{ route('store.product', $line['product']) }}" style="color: inherit; text-decoration: none;">
                    {{ $line['product']->name }}
                  </a>
                </h3>
                @if($line['product']->category)
                  <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">
                    {{ $line['product']->category->name }}
                  </div>
                @endif
                <div style="font-size: 18px; font-weight: 700; color: #3b82f6;">
                  ¥{{ number_format($line['price']) }}
                </div>
              </div>

              <!-- Quantity and Actions -->
              <div style="text-align: right;">
                <div style="margin-bottom: 12px;">
                  <label style="display: block; font-size: 12px; color: #6b7280; margin-bottom: 4px;">数量</label>
                  <input 
                    type="number" 
                    name="lines[{{ $line['product']->id }}]" 
                    value="{{ $line['qty'] }}" 
                    min="1"
                    style="width: 80px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; text-align: center;"
                  >
                </div>
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">
                  小計: ¥{{ number_format($line['price'] * $line['qty']) }}
                </div>
                <form method="post" action="{{ route('cart.remove') }}" style="display: inline;">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $line['product']->id }}">
                  <button 
                    type="submit"
                    style="padding: 6px 12px; background: #fee2e2; color: #991b1b; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;"
                  >
                    削除
                  </button>
                </form>
              </div>
            </div>
          @endforeach

          <button 
            type="submit"
            style="width: 100%; padding: 12px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 8px;"
          >
            数量を更新
          </button>
        </form>
      </div>

      <!-- Order Summary -->
      <div>
        <div style="padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; position: sticky; top: 20px;">
          <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 16px;">注文内容</h2>
          
          <div style="padding-bottom: 16px; border-bottom: 1px solid #e5e7eb; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
              <span style="color: #6b7280;">小計</span>
              <span style="font-weight: 600;">¥{{ number_format($subtotal) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span style="color: #6b7280;">送料</span>
              <span style="font-weight: 600;">
                @if($shipping > 0)
                  ¥{{ number_format($shipping) }}
                @else
                  無料
                @endif
              </span>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 24px;">
            <span style="font-size: 18px; font-weight: 700;">合計</span>
            <span style="font-size: 24px; font-weight: 700; color: #3b82f6;">¥{{ number_format($total) }}</span>
          </div>

          <form method="post" action="{{ route('checkout.start') }}">
            @csrf
            <button 
              type="submit"
              style="width: 100%; padding: 16px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: background 0.2s;"
              onmouseover="this.style.background='#2563eb'"
              onmouseout="this.style.background='#3b82f6'"
            >
              購入手続きへ進む
            </button>
          </form>

          <a 
            href="{{ route('store.index') }}" 
            style="display: block; text-align: center; padding: 12px; color: #6b7280; text-decoration: none; margin-top: 12px;"
          >
            買い物を続ける
          </a>
        </div>
      </div>
    </div>
  @endif
</div>
