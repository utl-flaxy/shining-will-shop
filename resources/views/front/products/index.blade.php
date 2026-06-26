<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
<h1 style="margin-bottom: 20px;">商品一覧</h1>

<!-- Search and Filter Form -->
<div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
  <form method="GET" action="{{ route('store.index') }}">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
      <!-- Keyword Search -->
      <div>
        <label style="display: block; margin-bottom: 4px; font-weight: 500;">キーワード検索</label>
        <input 
          type="text" 
          name="search" 
          value="{{ request('search') }}" 
          placeholder="商品名や説明文で検索"
          style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;"
        >
      </div>

      <!-- Category Filter -->
      <div>
        <label style="display: block; margin-bottom: 4px; font-weight: 500;">カテゴリ</label>
        <select 
          name="category" 
          style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;"
        >
          <option value="">すべてのカテゴリ</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <!-- Price Range Filter -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 120px; gap: 16px; align-items: end;">
      <div>
        <label style="display: block; margin-bottom: 4px; font-weight: 500;">最低価格</label>
        <input 
          type="number" 
          name="min_price" 
          value="{{ request('min_price') }}" 
          placeholder="0"
          min="0"
          style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;"
        >
      </div>

      <div>
        <label style="display: block; margin-bottom: 4px; font-weight: 500;">最高価格</label>
        <input 
          type="number" 
          name="max_price" 
          value="{{ request('max_price') }}" 
          placeholder="999999"
          min="0"
          style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;"
        >
      </div>

      <div>
        <button 
          type="submit" 
          style="width: 100%; padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;"
        >
          検索
        </button>
      </div>
    </div>

    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
      <div style="margin-top: 12px;">
        <a 
          href="{{ route('store.index') }}" 
          style="color: #6b7280; text-decoration: none; font-size: 14px;"
        >
          フィルタをクリア
        </a>
      </div>
    @endif
  </form>
</div>

<!-- Products Grid -->
@if($products->isEmpty())
  <p style="text-align: center; color: #6b7280; padding: 40px 0;">
    該当する商品が見つかりませんでした。
  </p>
@else
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
  @foreach($products as $p)
    <a href="{{ route('store.product',$p) }}" style="border:1px solid #eee;padding:8px;display:block;text-decoration:none;color:inherit;border-radius:8px;transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
      @php $thumb = $p->images[0] ?? null; @endphp
      @if($thumb)
        <img src="{{ asset('storage/'.$thumb) }}" style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:4px;">
      @else
        <div style="width:100%;aspect-ratio:1/1;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#999;">
          No Image
        </div>
      @endif
      <div style="margin-top:8px;font-weight:500;">{{ $p->name }}</div>
      @if($p->category)
        <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $p->category->name }}</div>
      @endif
      <div style="margin-top:4px;color:#3b82f6;font-weight:600;">¥{{ number_format($p->price) }}</div>
    </a>
  @endforeach
  </div>

  <!-- Pagination -->
  <div style="margin-top:24px;">
    {{ $products->appends(request()->query())->links() }}
  </div>
@endif
</div>
