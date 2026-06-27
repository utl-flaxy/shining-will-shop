<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>オーナーログイン | Shining Will Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/owner.css') }}" rel="stylesheet">
</head>
<body class="owner-login-body">
    <div class="owner-login-wrapper">
        <div class="owner-login-card">
            <h1 class="owner-login-title">オーナーログイン</h1>

            @if ($errors->any())
                <div class="owner-alert error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('owner.login.post') }}">
                @csrf

                <div class="owner-form-group">
                    <label for="email">メールアドレス</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="owner-form-group">
                    <label for="password">パスワード</label>
                    <input id="password" type="password" name="password" required>
                </div>

                <div class="owner-form-group inline">
                    <label>
                        <input type="checkbox" name="remember">
                        ログイン状態を保持する
                    </label>
                </div>

                <button type="submit" class="owner-btn-primary">
                    ログイン
                </button>
            </form>
        </div>
    </div>
</body>
</html>
