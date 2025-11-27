@props([
    // セッション切れ対策：ユーザーが取得できない場合も安全に動作
    'user' => filament()->auth()?->user(),
])

@php
    // 万が一 $user が null の場合、代替データを用意
    $userName = $user ? filament()->getUserName($user) : 'ゲスト';
    $userAvatarUrl = $user ? filament()->getUserAvatarUrl($user) : asset('images/default-avatar.png');
@endphp

<x-filament::avatar
    :src="$userAvatarUrl"
    :alt="__('filament-panels::layout.avatar.alt', ['name' => $userName])"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-user-avatar'])
    "
/>
