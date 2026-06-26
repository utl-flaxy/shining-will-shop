<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プロフィール</title>
</head>
<body>
<h1>プロフィール編集</h1>

@if (session('status') === 'profile-updated')
    <p>プロフィールを更新しました。</p>
@endif

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')
    <label>名前:
        <input type="text" name="name" value="{{ old('name', $user->name) }}">
    </label><br>
    <label>メール:
        <input type="email" name="email" value="{{ old('email', $user->email) }}">
    </label><br>
    <button type="submit">保存</button>
</form>

<form method="post" action="{{ route('profile.destroy') }}">
    @csrf
    @method('delete')
    <label>パスワード:
        <input type="password" name="password" required>
    </label>
    <button type="submit">アカウント削除</button>
</form>

</body>
</html>
