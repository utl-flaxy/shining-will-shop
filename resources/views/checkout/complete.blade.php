<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>購入完了 | Shining-Will</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">

  <header class="bg-pink-600 text-white text-center py-4 shadow">
    <h1 class="text-2xl font-bold">購入完了</h1>
  </header>

  <main class="container mx-auto px-4 py-10 text-center">
    <div class="bg-white rounded-lg shadow p-10">
      <h2 class="text-xl font-semibold mb-4">ご購入ありがとうございました！</h2>
      <p class="text-gray-600 mb-6">注文を受け付けました。</p>
      <a href="{{ url('/products') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded">
        商品一覧に戻る
      </a>
    </div>
  </main>

  <footer class="bg-gray-800 text-white text-center py-4 mt-10">
    <p class="text-sm">&copy; 2025 Shining-Will All Rights Reserved.</p>
  </footer>

</body>
</html>
