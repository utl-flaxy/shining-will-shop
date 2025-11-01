<h1>Products</h1>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
@foreach($products as $p)
  <a href="{{ route('store.product',$p) }}" style="border:1px solid #eee;padding:8px;display:block;text-decoration:none;color:inherit;">
    @php $thumb = $p->images[0] ?? null; @endphp
    @if($thumb)<img src="{{ asset('storage/'.$thumb) }}" style="width:100%;aspect-ratio:1/1;object-fit:cover">@endif
    <div style="margin-top:8px">{{ $p->name }}</div>
    <div>¥{{ number_format($p->price) }}</div>
  </a>
@endforeach
</div>
{{ $products->links() }}
