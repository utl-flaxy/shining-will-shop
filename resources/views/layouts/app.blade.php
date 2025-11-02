<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ショップ')</title>

    <!-- ちょっとだけ見た目整える（Bootstrap CDN） -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { padding-top: 60px; }
        .product-card { min-height: 180px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">アイドルグッズ店</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">カート</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    @if(session('flash'))
      <div class="alert alert-info">{{ session('flash') }}</div>
    @endif

    @yield('content')
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

async function postJson(url, data) {
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
    },
    body: JSON.stringify(data || {})
  });
  return res.json();
}

// 決済開始：/checkout/create に対してセッションのあるブラウザから POST し、返ってきた url に遷移
async function startCheckout() {
  try {
    const res = await postJson("{{ route('checkout.create') }}", {});
    if (res.error) {
      alert(res.error);
      return;
    }
    if (res.url) {
      // Stripe Checkout の外部 URL に遷移
      window.location.href = res.url;
    } else if (res.id && res.url) {
      window.location.href = res.url;
    } else {
      alert('決済セッションの作成に失敗しました');
      console.log(res);
    }
  } catch (e) {
    console.error(e);
    alert('決済開始中にエラーが発生しました');
  }
}
</script>

</body>
</html>
