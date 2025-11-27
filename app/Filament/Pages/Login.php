<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Auth\Login as BaseAuthLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseAuthLogin
{
    public function getTitle(): string|Htmlable
    {
        return __('ログイン');
    }

    protected function getEmailFormComponent(): \Filament\Forms\Components\Component
    {
        return parent::getEmailFormComponent()->label('メールアドレス');
    }

    protected function getPasswordFormComponent(): \Filament\Forms\Components\Component
    {
        return parent::getPasswordFormComponent()->label('パスワード');
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // ✅ Filamentのガードで認証
        if (! Filament::auth()->attempt(
            ['email' => $data['email'], 'password' => $data['password']],
            $data['remember'] ?? false,
        )) {
            $this->addError('email', __('メールアドレスまたはパスワードが正しくありません。'));
            return null;
        }

        // ✅ セッション再生成
        session()->regenerate();

        // ✅ Filament標準のレスポンスを返す（自動でダッシュボードへリダイレクト）
        return app(LoginResponse::class);
    }
}
