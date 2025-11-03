<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
  <!-- Breadcrumb -->
  <div style="margin-bottom: 20px;">
    <a href="{{ route('store.index') }}" style="color: #6b7280; text-decoration: none;">商品一覧</a>
    <span style="color: #6b7280; margin: 0 8px;">›</span>
    @if($product->category)
      <span style="color: #6b7280;">{{ $product->category->name }}</span>
      <span style="color: #6b7280; margin: 0 8px;">›</span>
    @endif
    <span>{{ $product->name }}</span>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
    <!-- Product Images -->
    <div>
      @php $imgs = $product->images ?? []; @endphp
      @if(count($imgs) > 0)
        <div style="margin-bottom: 12px;">
          <img 
            src="{{ asset('storage/'.$imgs[0]) }}" 
            style="width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;"
            alt="{{ $product->name }}"
          >
        </div>
        @if(count($imgs) > 1)
          <div style="display: flex; gap: 12px; overflow-x: auto;">
            @foreach(array_slice($imgs, 1) as $img)
              <img 
                src="{{ asset('storage/'.$img) }}" 
                style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #e5e7eb; cursor: pointer;"
                alt="{{ $product->name }}"
              >
            @endforeach
          </div>
        @endif
      @else
        <div style="width: 100%; aspect-ratio: 1/1; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999;">
          画像なし
        </div>
      @endif
    </div>

    <!-- Product Info -->
    <div>
      <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 16px;">{{ $product->name }}</h1>
      
      @if($product->category)
        <div style="margin-bottom: 12px;">
          <span style="display: inline-block; padding: 4px 12px; background: #f3f4f6; color: #6b7280; border-radius: 4px; font-size: 14px;">
            {{ $product->category->name }}
          </span>
        </div>
      @endif

      <div style="font-size: 32px; font-weight: 700; color: #3b82f6; margin-bottom: 24px;">
        ¥{{ number_format($product->price) }}
      </div>

      @if($product->description)
        <div style="margin-bottom: 24px; padding: 16px; background: #f9fafb; border-radius: 8px;">
          <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">商品説明</h3>
          <p style="color: #4b5563; line-height: 1.6; white-space: pre-wrap;">{{ $product->description }}</p>
        </div>
      @endif

      <div style="margin-bottom: 24px;">
        <div style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">在庫状況</div>
        <div style="font-weight: 600; {{ $product->stock > 0 ? 'color: #10b981;' : 'color: #ef4444;' }}">
          @if($product->stock > 0)
            在庫あり ({{ $product->stock }}点)
          @else
            在庫切れ
          @endif
        </div>
      </div>

      @if($product->stock > 0)
        <form method="post" action="{{ route('cart.add') }}" style="margin-bottom: 16px;">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          
          <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;">数量</label>
            <input 
              type="number" 
              name="qty" 
              value="1" 
              min="1" 
              max="{{ $product->stock }}"
              style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 16px;"
            >
          </div>
          
          <button 
            type="submit" 
            style="width: 100%; padding: 16px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: background 0.2s;"
            onmouseover="this.style.background='#2563eb'"
            onmouseout="this.style.background='#3b82f6'"
          >
            カートに入れる
          </button>
        </form>
      @else
        <div style="padding: 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; text-align: center; font-weight: 600;">
          この商品は現在お買い求めいただけません
        </div>
      @endif

      <a 
        href="{{ route('cart.index') }}" 
        style="display: block; text-align: center; padding: 12px; color: #3b82f6; text-decoration: none; border: 1px solid #3b82f6; border-radius: 8px; font-weight: 500; margin-top: 12px;"
      >
        カートを見る
      </a>
    </div>
  </div>
</div>
