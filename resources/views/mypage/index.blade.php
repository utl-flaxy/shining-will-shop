@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-12">

    <h1 class="text-3xl font-bold mb-8">
        マイページ
    </h1>

    <div class="bg-white rounded-xl shadow-md p-8">

        <div class="mb-8 border-b pb-6">
            <p class="text-sm text-gray-500">
                ようこそ
            </p>

            <h2 class="text-2xl font-bold mt-2">
                {{ auth()->user()->name }} さん
            </h2>

            <p class="text-gray-500 mt-2">
                {{ auth()->user()->email }}
            </p>
        </div>

        <div class="space-y-4">

            <a href="{{ route('mypage.orders') }}"
               class="flex items-center justify-between rounded-lg border p-5 hover:bg-gray-50 transition">
                <div>
                    <h3 class="font-semibold text-lg">
                        📦 注文履歴
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        ご注文履歴を確認できます
                    </p>
                </div>

                <span class="text-gray-400 text-xl">
                    →
                </span>
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center justify-between rounded-lg border p-5 hover:bg-gray-50 transition">
                <div>
                    <h3 class="font-semibold text-lg">
                        👤 プロフィール編集
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        名前・メールアドレス・パスワードを変更できます
                    </p>
                </div>

                <span class="text-gray-400 text-xl">
                    →
                </span>
            </a>

        </div>

        <div class="mt-10">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full rounded-lg bg-red-500 py-3 text-white font-semibold hover:bg-red-600 transition">
                    ログアウト
                </button>

            </form>

        </div>

    </div>

</div>
@endsection
