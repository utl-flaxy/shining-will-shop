<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', '管理画面 | SHINING WILL SHOP')</title>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
  @filamentStyles
</head>
<body>
  <div class="admin-layout">
    <aside class="sidebar">
      <div class="brand">
        <img src="{{ asset('images/logo_admin.png') }}" alt="logo" class="brand-logo">
        <h1>Shining Admin</h1>
      </div>

      <ul class="menu">
        <li><a href="/admin/dashboard">📊 ダッシュボード</a></li>
        <li><a href="/admin/products">📦 商品管理</a></li>
        <li><a href="/admin/orders">🧾 受注管理</a></li>
        <li><a href="/admin/customers">👥 顧客管理</a></li>
        <li><a href="/admin/analytics">📈 売上分析</a></li>
        <li><a href="/admin/settings">⚙️ 設定</a></li>
      </ul>
    </aside>

    <main class="content">
      <header class="admin-header">
        <h2>@yield('title')</h2>
        <div class="actions">@yield('actions')</div>
      </header>
      <div class="page-body">
        {{ $slot ?? '' }}
        @yield('content')
      </div>
    </main>
  </div>

  @filamentScripts
</body>
</html>
