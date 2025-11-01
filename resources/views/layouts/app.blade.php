<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Shop')</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    /* 最低限のスタイル（プロジェクトの CSS に合わせて調整してください） */
    body{font-family:ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto; margin:0; padding:0; background:#f7fafc;}
    .container{max-width:1100px; margin:28px auto; padding:0 16px;}
    header{display:flex; align-items:center; justify-content:space-between; gap:12px;}
    .logo{font-weight:700; font-size:18px;}
    nav a{margin-left:12px; color:#374151; text-decoration:none;}
    .flash{padding:8px 12px; background:#ecfccb; border-radius:6px; margin:12px 0;}
    .grid{display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:16px; margin-top:16px;}
    .card{background:#fff; border-radius:8px; padding:12px; box-shadow:0 6px 18px rgba(16,24,40,0.06); border:1px solid #eef2f7}
    .thumb{height:160px; object-fit:cover; width:100%; border-radius:6px; background:#f3f4f6;}
    .small-muted{color:#6b7280; font-size:0.9rem}
    .btn{background:#2563eb; color:#fff; border:none; padding:8px 10px; border-radius:6px; cursor:pointer}
    form.inline{display:inline;}
    table.cart{width:100%; border-collapse:collapse}
    table.cart td, table.cart th{padding:8px; border-bottom:1px solid #eef2f7}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="logo"><a href="{{ url('/') }}">Shining Will Shop</a></div>
      <nav>
        <a href="{{ route('products.index') }}">商品一覧</a>
        <a href="{{ route('cart.index') }}">カート ({{ array_sum(array_column(session('cart', []), 'qty') ?: [0]) }})</a>
      </nav>
    </header>

    @if(session('success'))
      <div class="flash">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="flash" style="background:#fee2e2;color:#991b1b">{{ session('error') }}</div>
    @endif

    <main>
      @yield('content')
    </main>

    <footer style="margin-top:36px; color:#6b7280; font-size:0.9rem">
      &copy; {{ date('Y') }} Shining Will Shop
    </footer>
  </div>
</body>
</html>
